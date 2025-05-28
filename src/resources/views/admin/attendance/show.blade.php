@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>
    <form method="POST" action="{{ route('admin.attendance.update', ['id' => $attendance->id]) }}">
        @csrf
        <input type="hidden" name="from" value="{{ $from }}">
        <div class="detail-card">
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td class="value-cell">{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="value-cell">
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</span>
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
                @php $i = 1; @endphp
                @foreach ($attendance->breakTimes as $break)
                <tr>
                    <th>{{ $i === 1 ? '休憩' : '休憩' . $i }}</th>
                    <td>
                        <input type="text" name="break_start_{{ $i }}"
                            value="{{ old("break_start_{$i}", \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}"> ～
                        <input type="text" name="break_end_{{ $i }}"
                            value="{{ old("break_end_{$i}", \Carbon\Carbon::parse($break->break_end)->format('H:i')) }}">
                        <br>
                        @error("break_start_{$i}")<div class="error">{{ $message }}</div>@enderror
                        @error("break_end_{$i}")<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
                @php $i++; @endphp
                @endforeach
                <tr>
                    <th>{{ $i === 1 ? '休憩' : '休憩' . $i }}</th>
                    <td>
                        <input type="text" name="break_start_{{ $i }}" value="{{ old("break_start_{$i}") }}"> ～
                        <input type="text" name="break_end_{{ $i }}" value="{{ old("break_end_{$i}") }}">
                        <br>
                        @error("break_start_{$i}")<div class="error">{{ $message }}</div>@enderror
                        @error("break_end_{$i}")<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="note">{{ old('note', $attendance->note) }}</textarea>
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
