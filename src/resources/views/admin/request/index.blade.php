@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_index.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="page-wrapper">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-menu">
        <a href="{{ route('request.index', ['status' => '承認待ち']) }}"
            class="{{ $currentStatus === '承認待ち' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('request.index', ['status' => '承認済み']) }}"
            class="{{ $currentStatus === '承認済み' ? 'active' : '' }}">承認済み</a>
    </div>

    <div class="card request-card">
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
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->status }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                        <td>{{ $request->note }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                        <td><a href="{{ url('/stamp_correction_request/approve/' . $request->id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">申請データがありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
