@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">2023年6月1日の勤怠</h2>

    <div class="month-switch">
        <a href="#">← 前日</a>
        <div><i class="fa fa-calendar"></i> 2023/06/01</div>
        <a href="#">翌日 →</a>
    </div>

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
@endsection
