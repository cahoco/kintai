<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Responses\VerifyEmailViewResponse as CustomVerifyEmailViewResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (FortifyLoginRequest $fortifyRequest) {
            $requestClass = request()->is('admin/*')
                ? AdminLoginRequest::class
                : LoginRequest::class;
            $customRequest = App::make($requestClass);
            $customRequest->setContainer(app())->setRedirector(app('redirect'));
            $customRequest->merge($fortifyRequest->all());
            Validator::make(
                $customRequest->all(),
                $customRequest->rules(),
                $customRequest->messages()
            )->validate();
            $user = \App\Models\User::where('email', $fortifyRequest->email)->first();
            if ($user && \Illuminate\Support\Facades\Hash::check($fortifyRequest->password, $user->password)) {
                return $user;
            }
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });

        Fortify::loginView(function () {
            return request()->is('admin/*')
                ? view('admin.login')
                : view('auth.login');
        });

        Fortify::registerView(fn () => view('auth.register'));
        app()->singleton(LoginResponseContract::class, function () {
            return new class implements LoginResponseContract {
                public function toResponse($request): RedirectResponse
                {
                    $user = auth()->user();
                    if (! $user->hasVerifiedEmail()) {
                        return redirect('/email/verify');
                    }
                    return $user->is_admin
                        ? redirect('/admin/attendance/list')
                        : redirect('/attendance');
                }
            };
        });

        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request): RedirectResponse
                {
                    $referer = $request->headers->get('referer');
                    if ($referer && str_contains($referer, '/admin/')) {
                        return redirect('/admin/login');
                    }
                    return redirect('/login');
                }
            };
        });

        $this->app->singleton(VerifyEmailViewResponse::class, CustomVerifyEmailViewResponse::class);
        app()->bind(FortifyLoginRequest::class, LoginRequest::class);

    }

}
