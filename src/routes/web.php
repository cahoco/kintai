<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::post('/attendance/{id}/request', [RequestController::class, 'store'])->name('request.store');
Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');

// 一般ユーザー用（DBのデータ）
Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('request.index');

// 管理者用（ダミーデータ or 本番用に切り替え予定）
Route::get('/admin/stamp_correction_request/list', [RequestController::class, 'indexAdmin'])->name('admin.request.index');
// ※ 認証ミドルウェアで「管理者と一般ユーザーで同じパスを使い分ける」構成にもできます。必要ならそちらも実装可能です。

Route::get('/stamp_correction_request/approve/{id}', [RequestController::class, 'showApprove']);

Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index']);

// 将来的に↓ミドルウェアで変更
Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'showByStaff']);

Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'show']);

// 一時的な表示専用ルート（nameをつけない）
Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout(); // セッション削除
    return redirect('/login')->with('success', 'ログアウトしました。');
})->name('logout');


Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');

