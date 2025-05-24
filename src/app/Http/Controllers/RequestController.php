<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        return view('request.index');
    }

        public function indexAdmin()
    {
        // ダミーデータ（本番ではモデルから取得）
        $requests = [
            (object)[
                'id' => 1,
                'status' => '承認待ち',
                'user_name' => '西怜奈',
                'target_date' => '2023/06/01',
                'reason' => '遅延のため',
                'requested_at' => '2023/06/02',
            ],
            (object)[
                'id' => 2,
                'status' => '承認待ち',
                'user_name' => '山田太郎',
                'target_date' => '2023/06/01',
                'reason' => '遅延のため',
                'requested_at' => '2023/08/02',
            ],
            (object)[
                'id' => 3,
                'status' => '承認待ち',
                'user_name' => '山田花子',
                'target_date' => '2023/06/02',
                'reason' => '遅延のため',
                'requested_at' => '2023/07/02',
            ],
        ];

        return view('admin.request.index', compact('requests'));
    }

    public function showApprove($id)
    {
        // 仮のダミーデータ（本番ではモデルで取得）
        $request = (object)[
            'id' => $id,
            'user_name' => '西 怜奈',
            'date' => '2023-06-01',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'break2_start' => '',
            'break2_end' => '',
            'note' => '電車遅延のため',
        ];

        return view('admin.request.approve', compact('request'));
    }

}
