<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\Admin\TemplateController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ApplicationController::class, 'index'])->name('applications.index');
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
Route::get('/applications/sent', [ApplicationController::class, 'sent'])->name('applications.sent');

// Video recording routes (token-gated)
Route::get('/record/{token}', [RecordController::class, 'show'])->name('record.show');
Route::post('/record/{token}/upload', [RecordController::class, 'upload'])->name('record.upload');
Route::get('/record/{token}/complete', [RecordController::class, 'complete'])->name('record.complete');

// Admin auth routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin panel routes (protected)
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.applications.index'));
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
    Route::get('/password', [AuthController::class, 'showChangePassword'])->name('password');
    Route::post('/password', [AuthController::class, 'changePassword'])->name('password.update');
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::patch('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
});
