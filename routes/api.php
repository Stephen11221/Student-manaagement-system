<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('attendance')->group(function () {
    Route::get('/summary', [AttendanceController::class, 'apiSummary'])->name('api.attendance.summary');
    Route::get('/records', [AttendanceController::class, 'apiRecords'])->name('api.attendance.records');
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('api.attendance.check-in');
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('api.attendance.check-out');
    Route::post('/bulk', [AttendanceController::class, 'bulkStore'])->name('api.attendance.bulk');
});
