<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $attendances = Attendance::with('user') // ユーザー名も取得
            ->where('date', $date)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'date' => $date,
        ]);
    }

    public function show($id)
    {
        $attendance = [
            'name' => '西 怜奈',
            'date' => '2023年6月1日',
            'start' => '09:00',
            'end' => '18:00',
            'break1_start' => '12:00',
            'break1_end' => '13:00',
            'break2_start' => '',
            'break2_end' => '',
            'note' => '電車遅延のため'
        ];

        return view('admin.attendance.show', compact('attendance'));
    }

    public function showByStaff($id, Request $request)
    {
        $user = User::findOrFail($id); // 対象ユーザー

        $month = $request->query('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get();

        return view('admin.attendance.staff_index', [
            'user' => $user,
            'attendances' => $attendances,
            'targetMonth' => $month,
        ]);
    }

    public function export($id, Request $request): StreamedResponse
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get();

        $csvFileName = "{$user->name}_{$month}_attendance.csv";

        $response = new StreamedResponse(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            // ヘッダー行
            fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

            foreach ($attendances as $attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '-';
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-';

                $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                    return $break->break_end
                        ? Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                        : 0;
                });

                $breakFormatted = $totalBreakMinutes
                    ? sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60)
                    : '-';

                $workFormatted = ($attendance->clock_in && $attendance->clock_out)
                    ? sprintf('%d:%02d',
                        floor((Carbon::parse($attendance->clock_out)->diffInMinutes($attendance->clock_in) - $totalBreakMinutes) / 60),
                        (Carbon::parse($attendance->clock_out)->diffInMinutes($attendance->clock_in) - $totalBreakMinutes) % 60
                    )
                    : '-';

                fputcsv($handle, [
                    Carbon::parse($attendance->date)->format('Y-m-d'),
                    $clockIn,
                    $clockOut,
                    $breakFormatted,
                    $workFormatted,
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$csvFileName}");

        return $response;
    }

}
