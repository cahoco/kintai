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
        $request = StampCorrectionRequest::with(['user', 'attendance', 'breakCorrections'])->findOrFail($id);
        return view('admin.request.approve', compact('request'));
    }

    public function store(CorrectionRequest $request, $id)
    {
        $correction = StampCorrectionRequest::create([
            'attendance_id' => $id,
            'user_id' => auth()->id(),
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'note' => $request->note,
            'status' => '承認待ち',
        ]);
        $index = 1;
        while ($request->has("break_start_{$index}") || $request->has("break_end_{$index}")) {
            $start = $request->input("break_start_{$index}");
            $end = $request->input("break_end_{$index}");
            if ($start && $end) {
                $correction->breakCorrections()->create([
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
            $index++;
        }
        return redirect()
            ->route('attendance.show', ['id' => $id])
            ->with('submitted', true);
    }

    public function sharedIndex(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', '承認待ち');
        if ($user->is_admin) {
            $requests = StampCorrectionRequest::with(['user', 'attendance'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('admin.request.index', [
                'requests' => $requests,
                'currentStatus' => $status,
            ]);
        } else {
            $requests = StampCorrectionRequest::with(['attendance'])
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
        $request = StampCorrectionRequest::with(['attendance', 'breakCorrections'])->findOrFail($id);
        $attendance = $request->attendance;
        $attendance->clock_in = $request->clock_in;
        $attendance->clock_out = $request->clock_out;
        $attendance->note = $request->note;
        $attendance->save();
        if ($request->breakCorrections->isNotEmpty()) {
            $attendance->breakTimes()->delete();
            foreach ($request->breakCorrections as $break) {
                $attendance->breakTimes()->create([
                    'break_start' => $break->break_start,
                    'break_end' => $break->break_end,
                ]);
            }
        }
        $request->status = '承認済み';
        $request->save();
        return redirect()->route('request.index', ['status' => '承認待ち']);
    }

}
