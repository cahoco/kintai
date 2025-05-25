<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.index');
    }

    public function show($id)
    {
        // いまは仮データでOK
        return view('attendance.show', ['id' => $id]);
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

        // 出勤記録を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('attendance.create');
        }

        // すでに未終了の休憩がある場合は新規作成しない
        $existingBreak = $attendance->breakTimes()->whereNull('break_end')->first();
        if ($existingBreak) {
            return redirect()->route('attendance.create');
        }

        // 新規休憩レコードを作成
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
