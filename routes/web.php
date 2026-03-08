<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\KhotibScheduleController;
use App\Http\Controllers\Admin\AdminActivityLogController;

Route::get('/', [HomeController::class, 'index']);
Route::view('/beranda', 'frontend.beranda');
Route::get("/khotib-jum'at", [HomeController::class, 'agenda'])->name('khotib.jumat');
Route::redirect('/agenda', "/khotib-jum'at");
Route::view('/agenda-maintenance', 'frontend.agenda-maintenance')->name('agenda.maintenance');
Route::get('/kalender-hijriyah', [HomeController::class, 'hijriCalendar'])->name('hijri.calendar');
Route::redirect('/jadwal-sholat', '/kalender-hijriyah');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:3,1')
    ->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::resource('/admin/khotib-schedules', KhotibScheduleController::class)->names('admin.khotib-schedules')->except(['show']);
    Route::get('/admin/khotib-schedules/download', [KhotibScheduleController::class, 'downloadPlainText'])->name('admin.khotib-schedules.download');
    Route::get('/admin/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::get('/admin/activity-logs/download', [AdminActivityLogController::class, 'downloadPlainText'])->name('admin.activity-logs.download');
});
