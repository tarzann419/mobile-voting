<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\VoterAccreditationController;
use App\Http\Controllers\OrganizationRegistrationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Organization registration routes
Route::get('/organization/register', [OrganizationRegistrationController::class, 'create'])->name('organization.register');
Route::post('/organization/register', [OrganizationRegistrationController::class, 'store']);

// Dashboard routes with role-based access
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/organizations', [AdminController::class, 'organizations'])->name('organizations.index');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/reports', [AdminController::class, 'systemReports'])->name('system.reports');
});

// Organization Admin routes
Route::middleware(['auth', 'verified', 'role:organization_admin'])->group(function () {
    // Election Management
    Route::resource('elections', ElectionController::class);
    Route::get('/elections/{election}/results', [ElectionController::class, 'results'])->name('elections.results');
    Route::get('/elections/{election}/reports', [ElectionController::class, 'reports'])->name('elections.reports');

    // Election Results and Reports Index Routes
    Route::get('/elections-results', [ElectionController::class, 'resultsIndex'])->name('elections.results.index');
    Route::get('/elections-reports', [ElectionController::class, 'reportsIndex'])->name('elections.reports.index');

    // Position Management
    Route::resource('positions', PositionController::class)->except(['show', 'create']);

    // Candidate Approval/Rejection (Admin only)
    Route::patch('/candidates/{candidate}/approve', [CandidateController::class, 'approve'])->name('candidates.approve');
    Route::patch('/candidates/{candidate}/reject', [CandidateController::class, 'reject'])->name('candidates.reject');

    // Admin candidate creation (creating candidates for users)
    Route::get('/admin/candidates/create', [CandidateController::class, 'adminCreate'])->name('admin.candidates.create');
    Route::post('/admin/candidates', [CandidateController::class, 'adminStore'])->name('admin.candidates.store');

    // Voter Accreditation
    Route::get('/voter-accreditation', [VoterAccreditationController::class, 'index'])->name('voter-accreditation.index');
    Route::get('/voter-accreditation/{accreditation}', [VoterAccreditationController::class, 'show'])->name('voter-accreditation.show');
    Route::post('/voter-accreditation/{accreditation}/approve', [VoterAccreditationController::class, 'approve'])->name('voter-accreditation.approve');
    Route::post('/voter-accreditation/{accreditation}/reject', [VoterAccreditationController::class, 'reject'])->name('voter-accreditation.reject');
});

// Voter routes
Route::middleware(['auth', 'verified', 'role:voter'])->group(function () {
    // Voting
    Route::get('/elections/{election}/ballot', [VotingController::class, 'ballot'])->name('voting.ballot');
    Route::post('/elections/{election}/vote', [VotingController::class, 'castVote'])->name('voting.cast');

    // Voter Accreditation Application
    Route::get('/voter/accreditation', [VoterAccreditationController::class, 'create'])->name('voter-accreditation.create');
    Route::post('/voter/accreditation', [VoterAccreditationController::class, 'store'])->name('voter-accreditation.store');
});

// Candidate registration routes (accessible by both voters and organization admins)
Route::middleware(['auth', 'verified', 'role:voter,organization_admin'])->group(function () {
    // Candidate Management (viewing and registration)
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');

    // Candidate Registration
    Route::get('/candidate/register', [CandidateController::class, 'create'])->name('candidates.register');
    Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
    Route::post('/candidate/register', [CandidateController::class, 'store'])->name('candidates.store');
});

// Shared routes (accessible by multiple roles)
Route::middleware(['auth', 'verified'])->group(function () {
    // Election viewing (all authenticated users can view elections)
    Route::get('/elections/{election}', [ElectionController::class, 'show'])->name('elections.show');
    Route::get('/elections/{election}/live-results', [ElectionController::class, 'liveResults'])->name('elections.live-results');
});

// Livewire components routes (handled by Livewire automatically)
// These are referenced in the dashboard views:
// - livewire.election-dashboard
// - livewire.voting-ballot  
// - livewire.candidate-registration
// - livewire.election-management
// - livewire.position-manager
// - livewire.voter-accreditation
// - livewire.real-time-results

require __DIR__ . '/auth.php';
