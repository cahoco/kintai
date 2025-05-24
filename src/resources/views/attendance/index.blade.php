@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">勤怠一覧</h2>

    <div class="month-switch">
        <a href="#" class="prev-month">← 前月</a>
        <div class="current-month">📅 2023/06</div>
        <a href="#" class="next-month">翌月 →</a>
    </div>

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
            @for ($i = 1; $i <= 30; $i++)
                <tr>
                    <td>06/{{ sprintf('%02d', $i) }} (木)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href="{{ url('/attendance/' . $i) }}">詳細</a></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@endsection
