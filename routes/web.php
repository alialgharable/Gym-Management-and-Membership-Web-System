<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDashboardController;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('bookings', BookingController::class);

Route::resource('members', MemberController::class)
    ->only(['index', 'show', 'destroy']);

Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])
    ->name('member.dashboard');