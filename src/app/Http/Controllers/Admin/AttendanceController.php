<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

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

    public function showByStaff($id)
    {
        $staff = [
            'id' => $id,
            'name' => '西 怜奈'
        ];

        return view('admin.attendance.staff_index', compact('staff'));
    }
}
