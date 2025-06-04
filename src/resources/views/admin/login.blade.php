@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
@php
    session(['url.intended' => '/admin/attendance/list']);
@endphp
<div class="register-container">
    <h2 class="register-title">管理者ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password">
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-submit">
            <button type="submit">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection
