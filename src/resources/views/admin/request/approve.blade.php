@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <div class="detail-card">
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td class="value-cell">{{ $request->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="value-cell">
                    {{ \Carbon\Carbon::parse($request->attendance->date)->format('Y年') }}　
                    <span style="margin-left: 40px;">{{ \Carbon\Carbon::parse($request->attendance->date)->format('n月j日') }}</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="value-cell">
                    {{ \Carbon\Carbon::parse($request->clock_in)->format('H:i') }} 〜
                    {{ \Carbon\Carbon::parse($request->clock_out)->format('H:i') }}
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td class="value-cell">
                    @if ($request->break_start_1 && $request->break_end_1)
                        {{ \Carbon\Carbon::parse($request->break_start_1)->format('H:i') }} 〜
                        {{ \Carbon\Carbon::parse($request->break_end_1)->format('H:i') }}
                    @else
                        - 〜 -
                    @endif
                </td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td class="value-cell">
                    @if ($request->break_start_2 && $request->break_end_2)
                        {{ \Carbon\Carbon::parse($request->break_start_2)->format('H:i') }} 〜
                        {{ \Carbon\Carbon::parse($request->break_end_2)->format('H:i') }}
                    @else
                        - 〜 -
                    @endif
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td class="value-cell">{{ $request->note }}</td>
            </tr>
        </table>
    </div>

    <div class="submit-button right-align">
        @if ($request->status === '承認済み')
            <button type="button" disabled style="background-color: #999;">承認済み</button>
        @else
            <form action="{{ route('request.approve', ['id' => $request->id]) }}" method="POST">
                @csrf
                <button type="submit">承認</button>
            </form>
        @endif
    </div>

</div>
@endsection
