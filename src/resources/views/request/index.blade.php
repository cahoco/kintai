@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-menu">
        <a href="#" class="active">承認待ち</a>
        <a href="#">承認済み</a>
    </div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 10; $i++)
                <tr>
                    <td>承認待ち</td>
                    <td>西怜奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href="{{ url('/attendance/' . $i) }}">詳細</a></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@endsection
