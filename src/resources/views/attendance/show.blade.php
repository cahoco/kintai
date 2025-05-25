@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <form method="POST" action="{{ route('request.store', ['id' => $attendance->id]) }}">
        @csrf
        <div class="detail-card">
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td class="value-cell">{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="value-cell">
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="text" name="clock_in" value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}"> ～
                        <input type="text" name="clock_out" value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                        <br>
                        @error('clock_in')<div class="error">{{ $message }}</div>@enderror
                        @error('clock_out')<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="text" name="break_start_1" value="{{ old('break_start_1', isset($attendance->breakTimes[0]) && $attendance->breakTimes[0]->break_start ? \Carbon\Carbon::parse($attendance->breakTimes[0]->break_start)->format('H:i') : '') }}"> ～
                        <input type="text" name="break_end_1" value="{{ old('break_end_1', isset($attendance->breakTimes[0]) && $attendance->breakTimes[0]->break_end ? \Carbon\Carbon::parse($attendance->breakTimes[0]->break_end)->format('H:i') : '') }}">
                        <br>
                        @error('break_start_1')<div class="error">{{ $message }}</div>@enderror
                        @error('break_end_1')<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
                <tr>
                    <th>休憩2</th>
                    <td>
                        <input type="text" name="break_start_2" value="{{ old('break_start_2', isset($attendance->breakTimes[1]) && $attendance->breakTimes[1]->break_start ? \Carbon\Carbon::parse($attendance->breakTimes[1]->break_start)->format('H:i') : '') }}"> ～
                        <input type="text" name="break_end_2" value="{{ old('break_end_2', isset($attendance->breakTimes[1]) && $attendance->breakTimes[1]->break_end ? \Carbon\Carbon::parse($attendance->breakTimes[1]->break_end)->format('H:i') : '') }}">
                        <br>
                        @error('break_start_2')<div class="error">{{ $message }}</div>@enderror
                        @error('break_end_2')<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="note">{{ old('note') }}</textarea>
                        @error('note')<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
            </table>
        </div>

        <div class="submit-button right-align">
                <button type="submit">修正</button>
        </div>

    </form>
</div>
@endsection
