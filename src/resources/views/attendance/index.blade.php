@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    <h2 class="page-title">勤怠一覧</h2>
    <div class="card month-card">
        <a href="{{ route('attendance.index', ['month' => $prevMonth]) }}" class="prev-month">
            <img src="{{ asset('storage/images/left-arrow.png') }}" alt="前月" class="arrow-icon"> 前月
        </a>
        <div class="current-month">
            <img src="{{ asset('storage/images/calender-icon.png') }}" alt="カレンダー" class="calender-icon">
            {{ $currentMonth }}
        </div>
        <a href="{{ route('attendance.index', ['month' => $nextMonth]) }}" class="next-month">
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
                    <tr>
                        @php
                            $weekMap = ['Sun' => '日', 'Mon' => '月', 'Tue' => '火', 'Wed' => '水', 'Thu' => '木', 'Fri' => '金', 'Sat' => '土'];
                            $carbon = \Carbon\Carbon::parse($attendance->date);
                            $dayOfWeek = $weekMap[$carbon->format('D')]; // 'Sun' → '日'
                        @endphp
                        <td>{{ $carbon->format('m/d') }} ({{ $dayOfWeek }})</td>
                        <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                        <td>
                            @php
                                $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                                    if ($break->break_end) {
                                        return \Carbon\Carbon::parse($break->break_end)->diffInMinutes($break->break_start);
                                    }
                                    return 0;
                                });
                                $breakFormatted = sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60);
                            @endphp
                            {{ $totalBreakMinutes ? $breakFormatted : '-' }}
                        </td>
                        <td>
                            @if ($attendance->clock_in && $attendance->clock_out)
                                @php
                                    $workMinutes = \Carbon\Carbon::parse($attendance->clock_out)->diffInMinutes($attendance->clock_in) - $totalBreakMinutes;
                                    $workFormatted = sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60);
                                @endphp
                                {{ $workFormatted }}
                            @else
                                -
                            @endif
                        </td>
                        <td><a href="{{ url('/attendance/' . $attendance->id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
