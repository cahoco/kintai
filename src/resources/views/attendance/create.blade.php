@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="stamp-container">
    <div class="status">{{ $status }}</div>
    <div class="date">{{ $today }}</div>
    <div class="time">{{ $now }}</div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    <div class="button-row">
        @if ($status === '勤務外')
            <form method="POST" action="{{ route('attendance.clock_in') }}">
                @csrf
                <button type="submit" class="stamp-button black">出勤</button>
            </form>
        @elseif ($status === '出勤中')
            <form method="POST" action="{{ route('attendance.clock_out') }}">
                @csrf
                <button type="submit" class="stamp-button black">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.break_start') }}">
                @csrf
                <button type="submit" class="stamp-button white">休憩入</button>
            </form>
        @elseif ($status === '休憩中')
            <form method="POST" action="{{ route('attendance.break_end') }}">
                @csrf
                <button type="submit" class="stamp-button white">休憩戻</button>
            </form>
        @endif
    </div>
</div>
@endsection
