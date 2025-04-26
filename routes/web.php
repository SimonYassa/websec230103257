<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\PurchasesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('products_home');
});

// Home page route
Route::get('/home', [ProductsController::class, 'home'])->name('products_home');

// Authentication routes
Route::get('/login', [UsersController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UsersController::class, 'login']);
Route::post('/do-login', [UsersController::class, 'login'])->name('do_login'); // For backward compatibility
Route::post('/logout', [UsersController::class, 'logout'])->name('logout');

// Registration routes (only for customers)
Route::get('/register', [UsersController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UsersController::class, 'register']);
Route::post('/do-register', [UsersController::class, 'register'])->name('do_register'); // For backward compatibility

// Password reset routes
Route::get('/password/reset', function() {
    return view('auth.passwords.email');
})->name('password_request');
Route::post('/password/email', function() {
    return back()->with('status', 'Password reset link sent!');
})->name('password_email');

// Password change routes
Route::get('/password/edit', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('/password/update', [UsersController::class, 'updatePassword'])->name('update_password');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Products routes - using underscore notation only
    Route::get('/products', [ProductsController::class, 'index'])->name('products_index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products_create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products_store');
    Route::get('/products/{product}', [ProductsController::class, 'show'])->name('products_show');
    Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products_edit');
    Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products_update');
    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products_destroy');
    Route::post('/products/{product}/purchase', [ProductsController::class, 'purchase'])->name('products_purchase');
    Route::get('/products/{product}/addstock', [ProductsController::class, 'addstock'])->name('add-stock');
    
    
    // Purchases routes - using underscore notation only
    Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases_index');
    Route::get('/purchases/{purchase}', [PurchasesController::class, 'show'])->name('purchases_show');
    Route::post('/purchases/{purchase}/update-quantity', [PurchasesController::class, 'updateQuantity'])->name('purchases_update_quantity');
    Route::delete('/purchases/{purchase}', [PurchasesController::class, 'destroy'])->name('purchases_destroy');
    
    // User profile routes
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::put('/profile', [UsersController::class, 'updateProfile'])->name('profile_update');
    
    // Employee routes (protected by checks in controller methods)
    Route::get('/users/create-employee', [UsersController::class, 'createEmployee'])->name('users_create_employee');
    Route::post('/users/store-employee', [UsersController::class, 'storeEmployee'])->name('users_store_employee');
    Route::get('/users/{user}/add-credit', [UsersController::class, 'showAddCredit'])->name('users_show_add_credit');
    Route::post('/users/{user}/add-credit', [UsersController::class, 'addCredit'])->name('users_add_credit');

    
    // Users routes - using underscore notation only
    Route::get('/users', [UsersController::class, 'index'])->name('users_index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users_create');
    Route::post('/users', [UsersController::class, 'store'])->name('users_store');
    Route::get('/users/{user}', [UsersController::class, 'show'])->name('users_show');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users_edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users_update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users_destroy');
});



  // Email Verification Routes
Route::get('/verify', [UsersController::class, 'verify'])->name('verify');
Route::get('/email/verify', [UsersController::class, 'showVerificationNotice'])->name('verification.notice');
Route::post('/email/verification-notification', [UsersController::class, 'resendVerification'])->name('verification.resend');

// Add this to routes/web.php
Route::get('/test-email', function () {
    $user = \App\Models\User::first();
    if (!$user) {
        return 'No users found to test with';
    }
    
    $token = \Illuminate\Support\Str::random(60);
    $link = route("verify", ['token' => $token]);
    
    \Illuminate\Support\Facades\Mail::to($user->email)
        ->send(new \App\Mail\VerificationEmail($link, $user->name));
    
    return 'Test email sent to ' . $user->email . '. Check Mailtrap inbox.';
});


// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Web\UsersController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Web\UsersController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Web\UsersController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Web\UsersController::class, 'resetPassword'])->name('password.update');

// Test routes
Route::get('/test', function () {
    return view('test');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/multable', function () {
    return view('multable');
});
