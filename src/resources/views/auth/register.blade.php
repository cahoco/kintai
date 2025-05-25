@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="register-container">
    <h2 class="register-title">会員登録</h2>
    <form method="POST" action="#">
        @csrf

        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name">
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email">
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
        </div>

        <div class="form-submit">
            <button type="submit">登録する</button>
        </div>

        <div class="auth-link">
            <a href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection
