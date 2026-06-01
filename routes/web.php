<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes (requires authentication)
Route::middleware("auth")->group(function () {
    
    // Plan routes
    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::post('subscription', [PlanController::class, 'subscription'])->name('subscription.create');
    
    // Subscription management routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('current', [SubscriptionController::class, 'current'])->name('current');
        Route::post('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::get('change-plan', [SubscriptionController::class, 'changePlanForm'])->name('change-plan.form');
        Route::post('change-plan', [SubscriptionController::class, 'changePlan'])->name('change-plan');
        Route::get('payment-method', [SubscriptionController::class, 'paymentMethodForm'])->name('payment-method.form');
        Route::post('payment-method', [SubscriptionController::class, 'updatePaymentMethod'])->name('payment-method.update');
    });
    
    // Invoice routes
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('download/{invoice}', [InvoiceController::class, 'download'])->name('download');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('subscriptions', [AdminDashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::get('revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
});