<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AuthController;

// 🧑‍💼 一般ユーザー専用ルート
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/{id}/request', [RequestController::class, 'store'])->name('request.store');

    Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('request.index');

    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
});

// 👨‍💼 管理者専用ルート
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'show']);
    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'showByStaff']);

    Route::get('/stamp_correction_request/list', [RequestController::class, 'indexAdmin'])->name('admin.request.index');
    Route::get('/stamp_correction_request/approve/{id}', [RequestController::class, 'showApprove']);
});

// 📝 認証関連（Fortifyの画面差し替え対応用）
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 🔐 管理者ログイン画面（Fortifyで統一予定だが一時的に残している場合）
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// 🚪 ログアウト共通
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

