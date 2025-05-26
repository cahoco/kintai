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

        // ?month=2025-06 のようなクエリを受け取る。なければ今月。
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // 勤怠データ取得（自分の当月分）
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

    public function show($id)
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);

        if ($attendance->user_id !== Auth::id()) {
            abort(403, '許可されていないアクセスです');
        }

        // 申請中の修正内容があれば取得
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

        // すでに出勤済みか確認
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('attendance.create');
        }

        // 新規出勤記録を作成
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
        $todayDate = Carbon::today();
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

        // 今日の出勤データを取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // 出勤していなければ退勤できない
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }

        // 現在時刻を退勤時刻として保存
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

        // 出勤していない or 退勤済みなら処理しない
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }

        // すでに未終了の休憩があるなら処理しない
        $existingBreak = $attendance->breakTimes()->whereNull('break_end')->first();
        if ($existingBreak) {
            return redirect()->route('attendance.create');
        }

        // ▼▼▼ 追加：出勤後すぐ（5分未満）なら休憩を許可しない ▼▼▼
        if ($attendance->clock_in) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $now = Carbon::now();
            if ($clockIn->diffInMinutes($now) < 5) {
                return redirect()->route('attendance.create')->with('error', '出勤直後の休憩はできません（5分以上経過後に操作してください）');
            }
        }

        // 休憩開始記録を作成
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
