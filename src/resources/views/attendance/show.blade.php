@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>
    @if ($correction)
    <div class="detail-card">
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td class="value-cell">{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="value-cell">
                    <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年 n月j日') }}</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="value-cell">
                    {{ $correction->clock_in ? \Carbon\Carbon::parse($correction->clock_in)->format('H:i') : '-' }}
                    ～
                    {{ $correction->clock_out ? \Carbon\Carbon::parse($correction->clock_out)->format('H:i') : '-' }}
                </td>
            </tr>
            @foreach ($correction->breakCorrections ?? [] as $index => $break)
            <tr>
                <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                <td class="value-cell">
                    {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}
                </td>
            </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td class="value-cell">{{ $correction->note }}</td>
            </tr>
        </table>
    </div>
    <div class="submit-button right-align">
        <p class="status-message">※承認待ちのため修正はできません。</p>
    </div>
    @else
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
                        <td class="value-cell date-cell">
                            <span class="year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                            <span class="month-day">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>出勤・退勤</th>
                        <td>
                            <input type="text" name="clock_in"
                                value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}"> ～
                            <input type="text" name="clock_out"
                                value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                            <br>
                            @error('clock_in')<div class="error">{{ $message }}</div>@enderror
                            @error('clock_out')<div class="error">{{ $message }}</div>@enderror
                        </td>
                    </tr>
                    @php
                        $breaks = $attendance->breakTimes;
                    @endphp
                    @foreach ($breaks as $index => $break)
                        <tr>
                            <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                            <td>
                                <input type="text" name="break_start_{{ $index + 1 }}"
                                    value="{{ old("break_start_".($index + 1), \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}"> ～
                                <input type="text" name="break_end_{{ $index + 1 }}"
                                    value="{{ old("break_end_".($index + 1), \Carbon\Carbon::parse($break->break_end)->format('H:i')) }}">
                                <br>
                                @error("break_start_" . ($index + 1))<div class="error">{{ $message }}</div>@enderror
                                @error("break_end_" . ($index + 1))<div class="error">{{ $message }}</div>@enderror
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>{{ $breaks->count() === 0 ? '休憩' : '休憩' . ($breaks->count() + 1) }}</th>
                        <td>
                            <input type="text" name="break_start_{{ $breaks->count() + 1 }}" value="{{ old("break_start_" . ($breaks->count() + 1)) }}"> ～
                            <input type="text" name="break_end_{{ $breaks->count() + 1 }}" value="{{ old("break_end_" . ($breaks->count() + 1)) }}">
                            <br>
                            @error("break_start_" . ($breaks->count() + 1))<div class="error">{{ $message }}</div>@enderror
                            @error("break_end_" . ($breaks->count() + 1))<div class="error">{{ $message }}</div>@enderror
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
    @endif
</div>
@endsection
