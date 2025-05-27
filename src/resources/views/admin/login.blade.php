@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
@php
    // 🔑 Fortifyの intended リダイレクト先を明示的にセット（管理者用）
    session(['url.intended' => '/admin/attendance/list']);
@endphp
<div class="register-container">
    <h2 class="register-title">管理者ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-submit">
            <button type="submit">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection
