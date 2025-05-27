@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($correction)
    {{-- 申請済み：確認表示モード --}}
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
                <td class="value-cell">
                    {{ $correction->clock_in ? \Carbon\Carbon::parse($correction->clock_in)->format('H:i') : '-' }}
                    ～
                    {{ $correction->clock_out ? \Carbon\Carbon::parse($correction->clock_out)->format('H:i') : '-' }}
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td class="value-cell">
                    @if ($correction->break_start_1 && $correction->break_end_1)
                        {{ \Carbon\Carbon::parse($correction->break_start_1)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($correction->break_end_1)->format('H:i') }}<br>
                    @endif
                    @if ($correction->break_start_2 && $correction->break_end_2)
                        {{ \Carbon\Carbon::parse($correction->break_start_2)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($correction->break_end_2)->format('H:i') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td class="value-cell">{{ $correction->note }}</td>
            </tr>
        </table>
    </div>

    {{-- 修正不可メッセージを右下に表示 --}}
    <div class="submit-button right-align">
        <p class="status-message">※承認待ちのため修正はできません。</p>
    </div>

@else
    {{-- 未申請：入力フォーム表示モード --}}
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
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</span>
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

                {{-- 休憩（既存分＋1行空欄） --}}
                @php $breakCount = $attendance->breakTimes->count(); @endphp
                @foreach ($attendance->breakTimes as $index => $break)
                    <tr>
                        <th>
                            {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                        </th>
                        <td>
                            <input type="text" name="break_start_{{ $index + 1 }}"
                                value="{{ old('break_start_' . ($index + 1), !empty($break->break_start) ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}"> ～
                            <input type="text" name="break_end_{{ $index + 1 }}"
                                value="{{ old('break_end_' . ($index + 1), !empty($break->break_end) ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                            <br>
                            @error("break_start_" . ($index + 1))<div class="error">{{ $message }}</div>@enderror
                            @error("break_end_" . ($index + 1))<div class="error">{{ $message }}</div>@enderror
                        </td>
                    </tr>
                @endforeach

                {{-- 空の追加行 --}}
                <tr>
                    <th>{{ $breakCount === 0 ? '休憩' : '休憩' . ($breakCount + 1) }}</th>
                    <td>
                        <input type="text" name="break_start_{{ $breakCount + 1 }}" value="{{ old('break_start_' . ($breakCount + 1)) }}"> ～
                        <input type="text" name="break_end_{{ $breakCount + 1 }}" value="{{ old('break_end_' . ($breakCount + 1)) }}">
                        <br>
                        @error("break_start_" . ($breakCount + 1))<div class="error">{{ $message }}</div>@enderror
                        @error("break_end_" . ($breakCount + 1))<div class="error">{{ $message }}</div>@enderror
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
