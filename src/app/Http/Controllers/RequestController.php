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
        $status = $request->query('status', '承認待ち');
        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('request.index', [
            'requests' => $requests,
            'currentStatus' => $status,
        ]);
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
        return redirect()
            ->route('attendance.show', ['id' => $id])
            ->with('submitted', true);
    }

    public function sharedIndex(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', '承認待ち');
        if ($user->is_admin) {
            $requests = \App\Models\StampCorrectionRequest::with(['user', 'attendance'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('admin.request.index', [
                'requests' => $requests,
                'currentStatus' => $status,
            ]);
        } else {
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
        $attendance = $request->attendance;
        $attendance->clock_in = $request->clock_in;
        $attendance->clock_out = $request->clock_out;
        $attendance->note = $request->note;
        $attendance->save();
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
        $request->status = '承認済み';
        $request->save();
        return redirect()->route('request.index', ['status' => '承認待ち']);
    }

}
