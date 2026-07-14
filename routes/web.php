<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PromotorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MidtransController;

Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', function () {
    return redirect('/pembeli');
});

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.process');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('auth.verify.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify.otp.process');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('auth.resend.otp');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::prefix('pembeli')->group(function () {
    Route::get('/', [PembeliController::class, 'home'])->name('pembeli.home');
    Route::get('/explore', [PembeliController::class, 'explore'])->name('pembeli.explore');
    Route::get('/event/{id}', [PembeliController::class, 'eventDetail'])->name('pembeli.event');
    Route::middleware(['auth', 'role:buyer'])->group(function () {
        Route::get('/checkout/{id}', [PembeliController::class, 'checkout'])->name('pembeli.checkout');
        Route::post('/event/{id}/checkout', [PembeliController::class, 'processCheckout'])->name('pembeli.checkout.process');
        Route::get('/payment/{id}', [PembeliController::class, 'payment'])->name('pembeli.payment');
        Route::get('/tickets', [PembeliController::class, 'tickets'])->name('pembeli.tickets');
        Route::get('/history', [PembeliController::class, 'history'])->name('pembeli.history');
        Route::get('/settings', [PembeliController::class, 'settings'])->name('pembeli.settings');
        Route::put('/settings', [PembeliController::class, 'updateSettings'])->name('pembeli.settings.update');
    });
});

Route::prefix('promotor')->middleware(['auth', 'role:promotor'])->group(function () {
    Route::get('/', [PromotorController::class, 'dashboard'])->name('promotor.dashboard');
    Route::get('/events', [PromotorController::class, 'events'])->name('promotor.events.index');
    Route::get('/events/{id}/checkins', [PromotorController::class, 'eventCheckins'])->name('promotor.events.checkins');
    Route::get('/events/create', [PromotorController::class, 'createEvent'])->name('promotor.events.create');
    Route::post('/events', [PromotorController::class, 'storeEvent'])->name('promotor.events.store');
    Route::get('/events/{id}/edit', [PromotorController::class, 'editEvent'])->name('promotor.events.edit');
    Route::put('/events/{id}', [PromotorController::class, 'updateEvent'])->name('promotor.events.update');
    Route::delete('/events/{id}', [PromotorController::class, 'destroyEvent'])->name('promotor.events.destroy');
    Route::get('/events/{id}/tickets', [PromotorController::class, 'manageTickets'])->name('promotor.events.tickets');
    Route::post('/events/{id}/tickets', [PromotorController::class, 'storeTicket'])->name('promotor.events.tickets.store');
    Route::put('/events/{id}/tickets/{ticket_id}', [PromotorController::class, 'updateTicket'])->name('promotor.events.tickets.update');
    Route::delete('/events/{id}/tickets/{ticket_id}', [PromotorController::class, 'destroyTicket'])->name('promotor.events.tickets.destroy');
    Route::get('/transactions', [PromotorController::class, 'transactions'])->name('promotor.transactions');
    Route::get('/scanner', [PromotorController::class, 'scanner'])->name('promotor.scanner');
    Route::post('/scanner/verify', [PromotorController::class, 'verifyScanner'])->name('promotor.scanner.verify');
    Route::get('/payouts', [PromotorController::class, 'payouts'])->name('promotor.payouts');
    Route::post('/payouts', [PromotorController::class, 'requestPayout'])->name('promotor.payouts.request');
    Route::get('/report', [PromotorController::class, 'report'])->name('promotor.report');
    Route::get('/settings', [PromotorController::class, 'settings'])->name('promotor.settings');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::get('/promotors', [AdminController::class, 'allPromotors'])->name('admin.promotors');
    Route::post('/promotors/{id}/approve', [AdminController::class, 'approvePromotor'])->name('admin.promotor.approve');
    Route::post('/promotors/{id}/toggle', [AdminController::class, 'togglePromotor'])->name('admin.promotors.toggle');
    Route::get('/promotors/{id}/edit', [AdminController::class, 'editPromotor'])->name('admin.promotors.edit');
    Route::put('/promotors/{id}', [AdminController::class, 'updatePromotor'])->name('admin.promotors.update');
    Route::delete('/promotors/{id}', [AdminController::class, 'destroyPromotor'])->name('admin.promotors.destroy');
    Route::get('/events', [AdminController::class, 'events'])->name('admin.events');
    Route::get('/events/create', [AdminController::class, 'createEvent'])->name('admin.events.create');
    Route::post('/events', [AdminController::class, 'storeEvent'])->name('admin.events.store');
    Route::get('/events/{id}/edit', [AdminController::class, 'editEvent'])->name('admin.events.edit');
    Route::put('/events/{id}', [AdminController::class, 'updateEvent'])->name('admin.events.update');
    Route::delete('/events/{id}', [AdminController::class, 'destroyEvent'])->name('admin.events.destroy');
    Route::post('/events/{id}/toggle', [AdminController::class, 'toggleEventStatus'])->name('admin.events.toggle');
    Route::get('/finance', [AdminController::class, 'finance'])->name('admin.finance');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/payouts', [AdminController::class, 'payouts'])->name('admin.payouts');
    Route::post('/payouts/{id}/approve', [AdminController::class, 'approvePayout'])->name('admin.payouts.approve');
    Route::post('/payouts/{id}/reject', [AdminController::class, 'rejectPayout'])->name('admin.payouts.reject');
    Route::post('/promotor/{id}/approve', [AdminController::class, 'approvePromotor'])->name('admin.promotor.approve');
});
