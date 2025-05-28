<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\CorrectionRequest;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $attendances = Attendance::with('user')
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
        $attendance = Attendance::with(['user', 'breakTimes'])->findOrFail($id);
        if (auth()->user()->is_admin) {
            return view('admin.attendance.show', compact('attendance'));
        } else {
            $correction = $attendance->correctionRequest;
            return view('attendance.show', compact('attendance', 'correction'));
        }
    }

    public function showByStaff($id, Request $request)
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

    public function update(CorrectionRequest $request, $id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        // 出退勤・備考を更新
        $attendance->clock_in = $request->input('clock_in');
        $attendance->clock_out = $request->input('clock_out');
        $attendance->note = $request->input('note');
        $attendance->save();

        // 古い休憩データを削除
        $attendance->breakTimes()->delete();

        // 入力された休憩を全てループで保存
        $index = 1;
        while (
            $request->has("break_start_{$index}") ||
            $request->has("break_end_{$index}")
        ) {
            $start = $request->input("break_start_{$index}");
            $end = $request->input("break_end_{$index}");

            // 両方埋まっていたときだけ保存
            if ($start && $end) {
                $attendance->breakTimes()->create([
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }

            $index++;
        }

        // リダイレクト先の分岐
        $from = $request->input('from');
        if ($from === 'staff') {
            return redirect()->route('admin.attendance.staff', ['id' => $attendance->user_id]);
        }
        return redirect()->route('admin.attendance.index');
    }

}
