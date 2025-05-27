@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/staff_index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    <h2 class="page-title">スタッフ一覧</h2>

    <div class="card attendance-card">
        <table class="staff-table">
            <thead>
                <tr>
                    <th class="name-col">名前</th>
                    <th class="email-col">メールアドレス</th>
                    <th class="action-col">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffList as $staff)
                    <tr>
                        <td class="name-col">{{ $staff->name }}</td>
                        <td class="email-col">{{ $staff->email }}</td>
                        <td class="action-col">
                            <a href="/admin/attendance/staff/{{ $staff->id }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
