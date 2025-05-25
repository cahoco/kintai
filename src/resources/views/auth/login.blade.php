@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="register-container">
    <h2 class="register-title">ログイン</h2>

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" value="{{ old('password') }}">
            @error('password')<div class="error">{{ $message }}</div>@enderror
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
