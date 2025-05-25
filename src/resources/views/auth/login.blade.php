@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="register-container">
    <h2 class="register-title">ログイン</h2>

    <form method="POST" action="#">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-submit">
            <button type="submit">ログインする</button>
        </div>
    </form>

    <div class="auth-link">
        <a href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection
