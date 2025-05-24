@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_index.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">{{ $staff['name'] }}さんの勤怠</h2>

    <div class="month-switch">
        <a href="#">← 前月</a>
        <div><i class="fa fa-calendar"></i> 2023/06</div>
        <a href="#">翌月 →</a>
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
            {{-- ダミーデータ --}}
            @foreach (range(1, 30) as $day)
            <tr>
                <td>06/{{ str_pad($day, 2, '0', STR_PAD_LEFT) }}({{ ['月','火','水','木','金','土','日'][($day + 5) % 7] }})</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>1:00</td>
                <td>8:00</td>
                <td><a href="#">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="submit-button">
        <button>CSV出力</button>
    </div>
</div>
@endsection
