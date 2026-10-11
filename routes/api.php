<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes
|--------------------------------------------------------------------------
|
| Stateless JSON API for the Flutter mobile application.
| Authenticated endpoints use Laravel Sanctum Bearer tokens.
|
*/

// Public Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-provider', [AuthController::class, 'registerProvider']);
Route::post('/google-login-mobile', [AuthController::class, 'googleLoginMobile']);

// Authenticated Endpoints
Route::middleware('auth:sanctum')->group(function () {
    // User profile and session
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Catalog data
    Route::get('/services', [CatalogController::class, 'services']);
    Route::get('/technicians', [CatalogController::class, 'technicians']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::post('/bookings/{booking}/rating', [BookingController::class, 'rating']);

    // Invoices and Payments
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}/payments', [InvoiceController::class, 'payments']);
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'submitPayment']);

    // Payment approvals
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve']);
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject']);
});
