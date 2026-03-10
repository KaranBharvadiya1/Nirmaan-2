<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractorBidController;
use App\Http\Controllers\ContractorPortfolioController;
use App\Http\Controllers\ContractorSettingsController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\OwnerBidController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerHireController;
use App\Http\Controllers\OwnerNotificationController;
use App\Http\Controllers\OwnerProjectController;
use App\Http\Controllers\OwnerSettingsController;
use App\Http\Controllers\OwnerShortlistController;
use Illuminate\Support\Facades\Route;

// Public landing page and marketing form routes.
Route::view('/', 'welcome')->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

// Authentication routes used by the landing-page modal and logout action.
Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Shared realtime messaging endpoints used by both owner and contractor panels.
Route::get('/firebase/custom-token', [MessagingController::class, 'issueFirebaseCustomToken'])
    ->middleware('auth')
    ->name('firebase.custom_token');
Route::post('/messages/attachments', [MessagingController::class, 'uploadChatAttachments'])
    ->middleware('auth')
    ->name('messages.attachments');

// Shared authenticated portfolio view opened from bids, hires, and messaging context.
Route::middleware('auth')
    ->get('/contractors/{contractor}/portfolio', [ContractorPortfolioController::class, 'showPublicPortfolio'])
    ->name('contractors.portfolio.show');

Route::middleware(['auth', 'role:Owner'])->prefix('owner')->name('owner.')->group(function (): void {
    // Owner dashboard overview routes.
    Route::get('/dashboard', [OwnerDashboardController::class, 'showDashboard'])->name('dashboard');

    // Owner project lifecycle routes.
    Route::get('/projects', [OwnerProjectController::class, 'showProjects'])->name('projects');
    Route::get('/projects/create', [OwnerProjectController::class, 'showCreateProjectForm'])->name('projects.create');
    Route::post('/projects', [OwnerProjectController::class, 'saveProject'])->name('projects.save');
    Route::get('/projects/{project}/edit', [OwnerProjectController::class, 'showProjectEditForm'])->name('projects.edit');
    Route::put('/projects/{project}', [OwnerProjectController::class, 'saveProjectChanges'])->name('projects.save_changes');
    Route::patch('/projects/{project}/status', [OwnerProjectController::class, 'changeProjectStatus'])->name('projects.change_status');
    Route::delete('/projects/{project}', [OwnerProjectController::class, 'deleteProject'])->name('projects.delete');
    Route::get('/projects/{project}', [OwnerProjectController::class, 'showProjectDetails'])->name('projects.details');

    // Owner bid review and hire-management routes.
    Route::get('/bids', [OwnerBidController::class, 'showReceivedBids'])->name('bids');
    Route::patch('/bids/{bid}/status', [OwnerBidController::class, 'changeBidStatus'])->name('bids.change_status');
    Route::get('/hires', [OwnerHireController::class, 'showOwnerHires'])->name('hires');
    Route::patch('/hires/{projectHire}/status', [OwnerHireController::class, 'saveOwnerHireStatus'])->name('hires.save_status');

    // Owner shortlist and decision support routes.
    Route::get('/shortlist', [OwnerShortlistController::class, 'index'])->name('shortlist.index');
    Route::post('/shortlist', [OwnerShortlistController::class, 'store'])->name('shortlist.store');
    Route::patch('/shortlist/{shortlist}', [OwnerShortlistController::class, 'update'])->name('shortlist.update');
    Route::delete('/shortlist/{shortlist}', [OwnerShortlistController::class, 'destroy'])->name('shortlist.destroy');

    // Owner communication and account settings routes.
    Route::get('/messages', [MessagingController::class, 'showOwnerMessages'])->name('messages');
    Route::get('/notifications', [OwnerNotificationController::class, 'index'])->name('notifications');
    Route::get('/settings', [OwnerSettingsController::class, 'showProfileSettings'])->name('settings');
    Route::put('/settings', [OwnerSettingsController::class, 'saveProfileSettings'])->name('settings.save');
});

Route::middleware(['auth', 'role:Contractor'])->prefix('contractor')->name('contractor.')->group(function (): void {
    // Contractor discovery, bid submission, and award tracking routes.
    Route::get('/projects', [ContractorBidController::class, 'showAvailableProjectsForBidding'])->name('projects');
    Route::get('/projects/{project}', [ContractorBidController::class, 'showProjectBidForm'])->name('projects.show');
    Route::post('/projects/{project}/bid', [ContractorBidController::class, 'submitProjectBid'])->name('projects.submit_bid');
    Route::get('/bids', [ContractorBidController::class, 'showMySubmittedBids'])->name('bids');
    Route::patch('/bids/{bid}/withdraw', [ContractorBidController::class, 'withdrawMyBid'])->name('bids.withdraw');
    Route::get('/awards', [ContractorBidController::class, 'showAwardedProjects'])->name('awards');

    // Contractor portfolio management routes.
    Route::get('/portfolio', [ContractorPortfolioController::class, 'showPortfolioIndex'])->name('portfolio');
    Route::get('/portfolio/create', [ContractorPortfolioController::class, 'showCreateForm'])->name('portfolio.create');
    Route::post('/portfolio', [ContractorPortfolioController::class, 'saveWork'])->name('portfolio.save');
    Route::get('/portfolio/{workSample}/edit', [ContractorPortfolioController::class, 'showEditForm'])->name('portfolio.edit');
    Route::put('/portfolio/{workSample}', [ContractorPortfolioController::class, 'saveWorkChanges'])->name('portfolio.update');
    Route::delete('/portfolio/{workSample}', [ContractorPortfolioController::class, 'deleteWork'])->name('portfolio.delete');

    // Contractor communication and account settings routes.
    Route::get('/messages', [MessagingController::class, 'showContractorMessages'])->name('messages');
    Route::get('/settings', [ContractorSettingsController::class, 'showProfileSettings'])->name('settings');
    Route::put('/settings', [ContractorSettingsController::class, 'saveProfileSettings'])->name('settings.save');
});
