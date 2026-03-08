<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractorBidController;
use App\Http\Controllers\OwnerBidController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerHireController;
use App\Http\Controllers\OwnerProjectController;
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

    Route::get('/projects', [OwnerProjectController::class, 'showProjects'])->name('projects');
    Route::get('/projects/create', [OwnerProjectController::class, 'showCreateProjectForm'])->name('projects.create');
    Route::post('/projects', [OwnerProjectController::class, 'saveProject'])->name('projects.save');
    Route::get('/projects/{project}/edit', [OwnerProjectController::class, 'showProjectEditForm'])->name('projects.edit');
    Route::put('/projects/{project}', [OwnerProjectController::class, 'saveProjectChanges'])->name('projects.save_changes');
    Route::patch('/projects/{project}/status', [OwnerProjectController::class, 'changeProjectStatus'])->name('projects.change_status');
    Route::delete('/projects/{project}', [OwnerProjectController::class, 'deleteProject'])->name('projects.delete');
    Route::get('/projects/{project}', [OwnerProjectController::class, 'showProjectDetails'])->name('projects.details');

    Route::get('/bids', [OwnerBidController::class, 'showReceivedBids'])->name('bids');
    Route::patch('/bids/{bid}/status', [OwnerBidController::class, 'changeBidStatus'])->name('bids.change_status');
    Route::get('/hires', [OwnerHireController::class, 'showOwnerHires'])->name('hires');
    Route::patch('/hires/{projectHire}/status', [OwnerHireController::class, 'saveOwnerHireStatus'])->name('hires.save_status');

    Route::get('/settings', [OwnerSettingsController::class, 'showProfileSettings'])->name('settings');
    Route::put('/settings', [OwnerSettingsController::class, 'saveProfileSettings'])->name('settings.save');
});

Route::middleware(['auth', 'role:Contractor'])->prefix('contractor')->name('contractor.')->group(function (): void {
    Route::get('/projects', [ContractorBidController::class, 'showAvailableProjectsForBidding'])->name('projects');
    Route::get('/projects/{project}', [ContractorBidController::class, 'showProjectBidForm'])->name('projects.show');
    Route::post('/projects/{project}/bid', [ContractorBidController::class, 'submitProjectBid'])->name('projects.submit_bid');
    Route::get('/bids', [ContractorBidController::class, 'showMySubmittedBids'])->name('bids');
    Route::patch('/bids/{bid}/withdraw', [ContractorBidController::class, 'withdrawMyBid'])->name('bids.withdraw');
    Route::get('/awards', [ContractorBidController::class, 'showAwardedProjects'])->name('awards');
});
