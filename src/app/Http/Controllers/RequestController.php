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
        $request = \App\Models\StampCorrectionRequest::with(['user', 'attendance'])->findOrFail($id);

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

        $status = $request->query('status', '承認待ち');

        if ($user->is_admin) {
            // 管理者：ステータスごとの申請一覧を取得
            $requests = \App\Models\StampCorrectionRequest::with(['user', 'attendance'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.request.index', [
                'requests' => $requests,
                'currentStatus' => $status,
            ]);
        } else {
            // 一般ユーザー：自分の申請のみ
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

    public function approve($id)
    {
        $request = StampCorrectionRequest::with('attendance')->findOrFail($id);

        // 勤怠データを更新
        $attendance = $request->attendance;
        $attendance->clock_in = $request->clock_in;
        $attendance->clock_out = $request->clock_out;
        $attendance->note = $request->note;
        $attendance->save();

        // 休憩データを更新（削除して再作成）
        $attendance->breakTimes()->delete();

        if ($request->break_start_1 && $request->break_end_1) {
            $attendance->breakTimes()->create([
                'break_start' => $request->break_start_1,
                'break_end' => $request->break_end_1,
            ]);
        }

        if ($request->break_start_2 && $request->break_end_2) {
            $attendance->breakTimes()->create([
                'break_start' => $request->break_start_2,
                'break_end' => $request->break_end_2,
            ]);
        }

        // ステータスを「承認済み」に変更
        $request->status = '承認済み';
        $request->save();

        return redirect()->route('request.index', ['status' => '承認待ち'])->with('success', '修正申請を承認しました。');
    }

}
