<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\VerifyEmailController;

Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('admin.login');

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest']);

    // 🔽 メール認証画面のルート（認証済ユーザーなら誰でもOK）
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'user', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/{id}/request', [RequestController::class, 'store'])->name('request.store');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/admin/staff/list', [StaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'showByStaff'])->name('admin.attendance.staff');
    Route::get('/admin/attendance/staff/{id}/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.staff.export');
    Route::get('/stamp_correction_request/approve/{id}', [RequestController::class, 'showApprove']);
    Route::post('/stamp_correction_request/approve/{id}', [RequestController::class, 'approve'])->name('request.approve'); // ←★これを追加
    Route::post('/attendance/{id}/update', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/stamp_correction_request/list', [RequestController::class, 'sharedIndex'])->name('request.index');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');