@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    @php
        use Carbon\Carbon;
        $carbonDate = Carbon::parse($date);
        $prev = $carbonDate->copy()->subDay()->toDateString();
        $next = $carbonDate->copy()->addDay()->toDateString();
    @endphp

    <h2 class="page-title">{{ $carbonDate->format('Y年n月j日') }}の勤怠</h2>

    <div class="card month-card">
        <a href="{{ url('/admin/attendance/list?date=' . $prev) }}" class="prev-month">
            <img src="{{ asset('storage/images/left-arrow.png') }}" alt="前日" class="arrow-icon"> 前日
        </a>
        <div class="current-month">
            <img src="{{ asset('storage/images/calender-icon.png') }}" alt="カレンダー" class="calender-icon">
            {{ $carbonDate->format('Y/m/d') }}
        </div>
        <a href="{{ url('/admin/attendance/list?date=' . $next) }}" class="next-month">
            翌日 <img src="{{ asset('storage/images/right-arrow.png') }}" alt="翌日" class="arrow-icon">
        </a>
    </div>

    <div class="card attendance-card">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
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
                    <td>{{ $attendance->user->name ?? '不明' }}</td>
                    <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                    <td>
                        @php
                            $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                                return $break->break_end
                                    ? \Carbon\Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                                    : 0;
                            });
                            $breakFormatted = $totalBreakMinutes ? sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60) : '-';
                        @endphp
                        {{ $breakFormatted }}
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
                    <td><a href="{{ url('/admin/attendance/' . $attendance->id) }}">詳細</a></td>
                </tr>
                @empty
                <tr><td colspan="6">データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
