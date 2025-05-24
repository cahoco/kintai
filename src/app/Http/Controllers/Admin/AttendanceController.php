<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // ダミーデータ
        $attendances = [
            ['name' => '山田 太郎', 'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
            ['name' => '西 怜奈',     'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
            ['name' => '山本 歌吉',   'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
        ];

        return view('admin.attendance.index', compact('attendances'));
    }

    public function show($id)
    {
        // ダミーデータ（$idは未使用）
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
        // 仮データ（後でDBと連携可能）
        $staff = [
            'id' => $id,
            'name' => '西 怜奈'
        ];

        return view('admin.attendance.staff_index', compact('staff'));
    }

}
