@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <div class="detail-card">
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>西　怜奈</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>
                    <span class="year">2023年</span>
                    <span class="month-day">6月1日</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="text" value="09:00"> ～ <input type="text" value="18:00">
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    <input type="text" value="12:00"> ～ <input type="text" value="13:00">
                </td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td>
                    <input type="text"> ～ <input type="text">
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>
                    <textarea>電車遅延のため</textarea>
                </td>
            </tr>
        </table>

        <div class="submit-button">
            <button type="submit">修正</button>
        </div>
    </div>
</div>
@endsection
