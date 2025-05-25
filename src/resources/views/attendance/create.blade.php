@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="stamp-container">
    <div class="status">勤務外</div>
    <div class="date">2023年6月1日(木)</div>
    <div class="time">08:00</div>

    <form method="POST" action="#">
        @csrf
        <button type="submit" class="stamp-button">出勤</button>
    </form>
</div>
@endsection
