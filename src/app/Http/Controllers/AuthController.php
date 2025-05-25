<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(RegisterRequest $request)
    {
        // バリデーション済みデータ取得
        $validated = $request->validated();

        // ユーザー登録
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 自動ログイン
        Auth::login($user);

        // 打刻画面にリダイレクト
        return redirect('/attendance');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // 認証成功 → 任意の画面へ
            return redirect('/attendance');
        }

        // 認証失敗
        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません',
        ])->withInput();
    }

}
