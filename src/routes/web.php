<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/attendance/list', [AttendanceController::class, 'index']);
Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
Route::get('/attendance', [AttendanceController::class, 'create']);