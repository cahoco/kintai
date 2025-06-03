<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    // メール認証画面の表示
    public function notice()
    {
        return view('auth.verify');
    }

    // メール認証リンクからの処理
    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/attendance');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect('/attendance')->with('verified', true);
    }

    // 再送信処理
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/attendance');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
