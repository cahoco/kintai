<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ time() }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/attendance" class="logo">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-img">
            </a>
            <nav class="nav">
                <a href="/attendance">勤怠</a>
                <a href="/attendance/list">勤怠一覧</a>
                <a href="/stamp_correction_request/list">申請</a>
                <a href="/logout">ログアウト</a>
            </nav>
            <nav class="nav">
                <a href="/admin/attendance/list">勤怠一覧</a>
                <a href="/admin/staff/list">スタッフ一覧</a> {{-- ← 追加 --}}
                <a href="/stamp_correction_request/list">申請一覧</a>
                <a href="/logout">ログアウト</a>
            </nav>
        </div>
    </header>

    {{-- コンテンツ --}}
    <main>
        @yield('content')
    </main>
</body>
</html>
