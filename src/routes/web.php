<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AuthController;

Route::get('/attendance/list', [AttendanceController::class, 'index']);
Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
Route::get('/attendance', [AttendanceController::class, 'create']);

Route::get('/stamp_correction_request/list', [RequestController::class, 'index']);
Route::get('/stamp_correction_request/list', [RequestController::class, 'indexAdmin']);
// ※ 認証ミドルウェアで「管理者と一般ユーザーで同じパスを使い分ける」構成にもできます。必要ならそちらも実装可能です。

Route::get('/stamp_correction_request/approve/{id}', [RequestController::class, 'showApprove']);

Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index']);

Route::get('/admin/staff/list', [StaffController::class, 'index']);
Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'showByStaff']);

Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'show']);

// 一時的な表示専用ルート（nameをつけない）
Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');