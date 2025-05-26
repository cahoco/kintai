@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    @php
        $carbonDate = \Carbon\Carbon::parse($date);
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
                {{-- 仮データ --}}
                @foreach (range(1, 7) as $i)
                <tr>
                    <td>山田 太郎</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href="{{ url('/admin/attendance/' . $i) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
