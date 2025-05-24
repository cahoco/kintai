<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;

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