@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">スタッフ一覧</h2>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffList as $staff)
            <tr>
                <td>{{ $staff['name'] }}</td>
                <td>{{ $staff['email'] }}</td>
                <td>
                    <a href="{{ url('/admin/attendance/staff/' . $staff['id']) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
