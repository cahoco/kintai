<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔐 ログイン処理のカスタマイズ（DBからユーザー取得＋パスワード確認）
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => __('メールアドレスまたはパスワードが間違っています。'),
            ]);
        });

        // 🖥️ 自作ログイン・登録画面の指定
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        app()->singleton(LoginResponseContract::class, function () {
        return new class implements LoginResponseContract {
            public function toResponse($request): RedirectResponse
            {
                $user = auth()->user();

                if ($user->is_admin) {
                    return redirect()->intended('/admin/attendance/list'); // 管理者
                }

                return redirect()->intended('/attendance'); // 一般ユーザー
            }
            };
        });
    }
}
