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
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔐 ログイン処理のカスタマイズ
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => __('メールアドレスまたはパスワードが間違っています。'),
            ]);
        });

        // ✅ URLによってログイン画面を切り替える
        Fortify::loginView(function () {
            return request()->is('admin/*')
                ? view('admin.login')   // 管理者ログイン用
                : view('auth.login');   // 一般ユーザー用
        });

        // 登録画面は共通で auth.register を使用
        Fortify::registerView(fn () => view('auth.register'));

        // ✅ ログイン後のリダイレクト先を切り替え
        app()->singleton(LoginResponseContract::class, function () {
            return new class implements LoginResponseContract {
                public function toResponse($request): RedirectResponse
                {
                    $user = auth()->user();
                    return $user->is_admin
                        ? redirect()->intended('/admin/attendance/list')
                        : redirect()->intended('/attendance');
                }
            };
        });

        // 登録処理のバインド
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        // ✅ ログアウト後のリダイレクトを明示（← ここを追加）
        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request): RedirectResponse
                {
                    // 直前のURLを参照
                    $referer = $request->headers->get('referer');

                    // 管理者ページからのログアウトか判定
                    if ($referer && str_contains($referer, '/admin/')) {
                        return redirect('/admin/login');
                    }

                    return redirect('/login');
                }
            };
        });

    }
}
