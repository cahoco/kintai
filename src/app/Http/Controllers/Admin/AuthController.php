<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // 条件：管理者のみに限定（例：emailドメインなど）
        if (Auth::attempt($credentials)) {
            // 管理者判定（例：emailがadmin専用ならOK）
            if (Auth::user()->is_admin) {
                return redirect('/admin/attendance/list')->with('success', '管理者ログインしました。');
            }

            Auth::logout(); // 一般ユーザーだったらログアウト
            return back()->withErrors(['email' => '管理者権限がありません']);
        }

        return back()->withErrors(['email' => 'ログイン情報が正しくありません'])->withInput();
    }

}
