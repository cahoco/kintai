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
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->user_name }}</td>
                    <td>{{ $request->target_date }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->requested_at }}</td>
                    <td><a href="{{ url('/stamp_correction_request/approve/' . $request->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
