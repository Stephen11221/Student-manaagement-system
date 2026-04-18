<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('attendance')->group(function () {
    Route::get('/summary', [AttendanceController::class, 'apiSummary'])->name('api.attendance.summary');
    Route::get('/records', [AttendanceController::class, 'apiRecords'])->name('api.attendance.records');
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('api.attendance.check-in');
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('api.attendance.check-out');
    Route::post('/bulk', [AttendanceController::class, 'bulkStore'])->name('api.attendance.bulk');
});

Route::middleware('auth')->prefix('accounting')->group(function () {
    Route::get('/summary', [AccountingController::class, 'apiSummary'])->name('api.accounting.summary');
    Route::get('/accounts', [AccountingController::class, 'apiAccounts'])->name('api.accounting.accounts');
    Route::get('/invoices', [AccountingController::class, 'apiInvoices'])->name('api.accounting.invoices');
    Route::get('/ledger/{account}', [AccountingController::class, 'apiLedger'])->name('api.accounting.ledger');
    Route::post('/transactions', [AccountingController::class, 'storeJournal'])->name('api.accounting.transactions.store');
    Route::post('/invoices', [AccountingController::class, 'storeInvoice'])->name('api.accounting.invoices.store');
    Route::post('/invoices/{invoice}/payments', [AccountingController::class, 'recordPayment'])->name('api.accounting.payments.store');
});
