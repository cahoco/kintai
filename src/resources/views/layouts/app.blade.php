<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>勤怠アプリ</title>
        <link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ time() }}">
        @yield('css')
    </head>

    @php
        $bodyClass = Auth::check() ? 'body bg-logged-in' : 'body';
    @endphp

    <body class="{{ $bodyClass }}">
        @php
            use Illuminate\Support\Facades\Auth;
        @endphp
        @if (Auth::check() || request()->is('login') || request()->is('register') || request()->is('admin/login') || request()->is('email/verify'))
            <header class="header">
                <div class="header-inner">
                    <a href="{{ Auth::check() && Auth::user()->is_admin ? '/admin/attendance/list' : '/attendance' }}" class="logo">
                        <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECH" class="logo-img">
                    </a>
                    @if (Auth::check() && !request()->is('email/verify'))
                        @if (!Auth::user()->is_admin)
                            <nav class="nav">
                                <a href="/attendance">勤怠</a>
                                <a href="/attendance/list">勤怠一覧</a>
                                <a href="/stamp_correction_request/list">申請</a>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="logout-button">ログアウト</button>
                                </form>
                            </nav>
                        @else
                            <nav class="nav">
                                <a href="/admin/attendance/list">勤怠一覧</a>
                                <a href="/admin/staff/list">スタッフ一覧</a>
                                <a href="/stamp_correction_request/list">申請一覧</a>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="logout-button">ログアウト</button>
                                </form>
                            </nav>
                        @endif
                    @endif
                </div>
            </header>
        @endif
        <main>
            @yield('content')
        </main>
    </body>

</html>
