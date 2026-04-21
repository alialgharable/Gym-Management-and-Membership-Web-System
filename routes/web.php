<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\GymClassController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerDashboardController;
use App\Http\Controllers\TrainerApplicationController;
use App\Http\Controllers\TrainerReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FinanceController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about-us', 'about')->name('about');

Route::middleware(['auth'])->group(function () {
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

    Route::get('trainer-applications/create', [TrainerApplicationController::class, 'create'])
        ->name('trainer-applications.create');

    Route::post('trainer-applications', [TrainerApplicationController::class, 'store'])
        ->name('trainer-applications.store');

    Route::get('my-trainer-application', [TrainerApplicationController::class, 'myApplication'])
        ->name('trainer-applications.mine');

    Route::get('trainer-applications/{trainerApplication}', [TrainerApplicationController::class, 'show'])
        ->name('trainer-applications.show');

    Route::get('trainer-applications/{trainerApplication}/edit', [TrainerApplicationController::class, 'edit'])
        ->name('trainer-applications.edit');

    Route::put('trainer-applications/{trainerApplication}', [TrainerApplicationController::class, 'update'])
        ->name('trainer-applications.update');

    Route::delete('trainer-applications/{trainerApplication}', [TrainerApplicationController::class, 'destroy'])
        ->name('trainer-applications.destroy');

    Route::get('/trainers/{trainer}/edit', [TrainerController::class, 'edit'])->name('trainers.edit');
    Route::put('/trainers/{trainer}', [TrainerController::class, 'update'])->name('trainers.update');
    Route::delete('/trainers/{trainer}', [TrainerController::class, 'destroy'])->name('trainers.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/finance', [FinanceController::class, 'index'])->name('admin.finance');
    Route::resource('plans', MembershipPlanController::class)->only(['create', 'edit']);
    Route::resource('admins', AdminController::class)->except(['index', 'show']);
    Route::resource('subscriptions', SubscriptionController::class)->except(['store']);
    Route::resource('members', MemberController::class)->except(['index', 'show', 'create']);

    Route::get('trainer-applications', [TrainerApplicationController::class, 'index'])
        ->name('trainer-applications.index');

    Route::patch('trainer-applications/{trainerApplication}/accept', [TrainerApplicationController::class, 'accept'])
        ->name('trainer-applications.accept');

    Route::patch('trainer-applications/{trainerApplication}/reject', [TrainerApplicationController::class, 'reject'])
        ->name('trainer-applications.reject');
});

Route::middleware(['auth', 'trainer'])->group(function () {
    Route::get('/trainer/dashboard', [TrainerDashboardController::class, 'index'])->name('trainer.dashboard');
    Route::resource('classes', GymClassController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'member'])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::resource('members', MemberController::class)->only(['edit', 'update', 'destroy']);
});

Route::resource('bookings', BookingController::class);
Route::resource('members', MemberController::class)->only(['index', 'show']);
Route::resource('classes', GymClassController::class)->only(['index', 'show']);
Route::resource('plans', MembershipPlanController::class)->only(['index', 'show']);
Route::resource('trainers', TrainerController::class)->only(['index', 'show']);
Route::resource('reviews', TrainerReviewController::class);
Route::resource('admins', AdminController::class)->only(['index', 'show']);