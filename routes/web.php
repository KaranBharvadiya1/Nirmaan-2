<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerSettingsController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:Owner'])->prefix('owner')->name('owner.')->group(function (): void {
    Route::get('/dashboard', [OwnerDashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/settings', [OwnerSettingsController::class, 'showProfileSettings'])->name('settings');
    Route::put('/settings', [OwnerSettingsController::class, 'saveProfileSettings'])->name('settings.save');
});
