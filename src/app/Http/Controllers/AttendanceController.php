<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();
        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get();
        return view('attendance.index', [
            'attendances' => $attendances,
            'currentMonth' => $startOfMonth->format('Y/m'),
            'prevMonth' => $startOfMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $startOfMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function show($id, Request $request)
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);
        if (!Auth::user()->is_admin && $attendance->user_id !== Auth::id()) {
            abort(403, '許可されていないアクセスです');
        }
        $from = $request->query('from', 'list');
        if (Auth::user()->is_admin) {
            return view('admin.attendance.show', [
                'attendance' => $attendance,
                'from' => $from,
            ]);
        }
        $correction = \App\Models\StampCorrectionRequest::where('attendance_id', $id)
            ->where('user_id', Auth::id())
            ->where('status', '承認待ち')
            ->latest()
            ->first();
        return view('attendance.show', [
            'attendance' => $attendance,
            'correction' => $correction,
        ]);
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if ($existing) {
            return redirect()->route('attendance.create');
        }
        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => Carbon::now()->format('H:i:s'),
        ]);
        return redirect()->route('attendance.create');
    }

    public function create()
    {
        $user = Auth::user();
        $todayDate = Carbon::now()->toDateString();
        $todayText = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');
        $now = Carbon::now()->format('H:i');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $todayDate)
            ->first();

        $status = '勤務外';
        if ($attendance) {
            $hasBreak = $attendance->breakTimes()->whereNull('break_end')->exists();
            if ($attendance->clock_out) {
                $status = '退勤済';
            } elseif ($hasBreak) {
                $status = '休憩中';
            } else {
                $status = '出勤中';
            }
        }

        return view('attendance.create', [
            'status' => $status,
            'today' => $todayText,
            'now' => $now,
        ]);
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }
        $attendance->update([
            'clock_out' => Carbon::now()->format('H:i:s'),
        ]);
        return redirect()->route('attendance.create')->with('success', 'お疲れ様でした。');
    }

    public function breakStart()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }
        $existingBreak = $attendance->breakTimes()->whereNull('break_end')->first();
        if ($existingBreak) {
            return redirect()->route('attendance.create');
        }
        $attendance->breakTimes()->create([
            'break_start' => Carbon::now()->format('H:i:s'),
        ]);
        return redirect()->route('attendance.create');
    }

    public function breakEnd()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }
        $ongoingBreak = $attendance->breakTimes()->whereNull('break_end')->latest()->first();
        if (!$ongoingBreak) {
            return redirect()->route('attendance.create');
        }
        $ongoingBreak->update([
            'break_end' => Carbon::now()->format('H:i:s'),
        ]);
        return redirect()->route('attendance.create');
    }

}
