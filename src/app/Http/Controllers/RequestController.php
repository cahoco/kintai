<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use App\Models\StampCorrectionRequest;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // クエリパラメータからステータス取得（デフォルトは 承認待ち）
        $status = $request->query('status', '承認待ち');

        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('request.index', [
            'requests' => $requests,
            'currentStatus' => $status, // Blade 側で使う
        ]);
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

    public function store(CorrectionRequest $request, $id)
    {
        StampCorrectionRequest::create([
            'attendance_id' => $id,
            'user_id' => auth()->id(),
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'break_start_1' => $request->break_start_1,
            'break_end_1' => $request->break_end_1,
            'break_start_2' => $request->break_start_2,
            'break_end_2' => $request->break_end_2,
            'note' => $request->note,
            'status' => '承認待ち',
        ]);

        // 勤怠詳細画面に「申請済み」状態で戻る
        return redirect()
            ->route('attendance.show', ['id' => $id])
            ->with('submitted', true);
    }

    public function sharedIndex(Request $request)
    {
        $user = auth()->user();

        if ($user->is_admin) {
            // 管理者：すべての申請を取得
            $requests = \App\Models\StampCorrectionRequest::with(['user', 'attendance'])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.request.index', compact('requests'));
        } else {
            // 一般ユーザー：自分の申請のみ
            $status = $request->query('status', '承認待ち');

            $requests = \App\Models\StampCorrectionRequest::with(['attendance'])
                ->where('user_id', $user->id)
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('request.index', [
                'requests' => $requests,
                'currentStatus' => $status,
            ]);
        }
    }

}
