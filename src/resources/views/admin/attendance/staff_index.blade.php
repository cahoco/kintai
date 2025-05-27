@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    @php
        use Carbon\Carbon;
        $month = Carbon::parse($targetMonth);
        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');
    @endphp

    <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>

    <div class="card month-card">
        <a href="{{ url("/admin/attendance/staff/{$user->id}?month={$prevMonth}") }}">
            <img src="{{ asset('storage/images/left-arrow.png') }}" alt="前月" class="arrow-icon"> 前月
        </a>
        <div>
            <img src="{{ asset('storage/images/calender-icon.png') }}" alt="カレンダー" class="calender-icon">
            {{ $month->format('Y/m') }}
        </div>
        <a href="{{ url("/admin/attendance/staff/{$user->id}?month={$nextMonth}") }}">
            翌月 <img src="{{ asset('storage/images/right-arrow.png') }}" alt="翌月" class="arrow-icon">
        </a>
    </div>

    <div class="card attendance-card">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    @php
                        $date = Carbon::parse($attendance->date)->format('m/d(D)');
                        $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '-';
                        $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-';

                        $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                            return $break->break_end
                                ? Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                                : 0;
                        });

                        $breakFormatted = $totalBreakMinutes
                            ? sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60)
                            : '-';

                        $workFormatted = ($attendance->clock_in && $attendance->clock_out)
                            ? sprintf('%d:%02d',
                                floor((Carbon::parse($attendance->clock_out)->diffInMinutes($attendance->clock_in) - $totalBreakMinutes) / 60),
                                (Carbon::parse($attendance->clock_out)->diffInMinutes($attendance->clock_in) - $totalBreakMinutes) % 60
                              )
                            : '-';
                    @endphp
                    <tr>
                        @php
                            $weekMap = ['Sun' => '日', 'Mon' => '月', 'Tue' => '火', 'Wed' => '水', 'Thu' => '木', 'Fri' => '金', 'Sat' => '土'];
                            $carbon = \Carbon\Carbon::parse($attendance->date);
                            $dayOfWeek = $weekMap[$carbon->format('D')];
                        @endphp
                        <td>{{ $carbon->format('m/d') }} ({{ $dayOfWeek }})</td>
                        <td>{{ $clockIn }}</td>
                        <td>{{ $clockOut }}</td>
                        <td>{{ $breakFormatted }}</td>
                        <td>{{ $workFormatted }}</td>
                        <td><a href="{{ url("/admin/attendance/{$attendance->id}") }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

        <input type="hidden" name="month" value="{{ $targetMonth }}">
        <div class="export-button-wrapper">
            <form action="{{ route('admin.attendance.staff.export', ['id' => $user->id]) }}" method="GET">
                <button type="submit" class="export-button">CSV出力</button>
            </form>
        </div>
</div>
@endsection
