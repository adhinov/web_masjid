<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\KhotibScheduleController;

Route::get('/', [HomeController::class, 'index']);
Route::view('/beranda', 'frontend.beranda');
Route::get('/agenda', [HomeController::class, 'agenda'])->name('agenda');
Route::get('/kalender-hijriyah', [HomeController::class, 'hijriCalendar'])->name('hijri.calendar');
Route::redirect('/jadwal-sholat', '/kalender-hijriyah');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::resource('/admin/khotib-schedules', KhotibScheduleController::class)->names('admin.khotib-schedules')->except(['show']);
});
