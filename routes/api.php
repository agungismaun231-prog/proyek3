<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;


/*
|--------------------------------------------------------------------------
| Mobile REST API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-provider', [AuthController::class, 'registerProvider']);
Route::post('/google-login-mobile', [AuthController::class, 'googleLoginMobile']);

// Authenticated Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get(
    '/notifications',
    [NotificationController::class, 'index']
);

Route::put(
    '/notifications/{notification}/read',
    [NotificationController::class, 'markAsRead']
);

Route::get(
    '/notifications/unread-count',
    [NotificationController::class, 'unreadCount']
);

    // User profile and session
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Catalog data
    Route::get('/services', [CatalogController::class, 'services']);
    Route::get('/technicians', [CatalogController::class, 'technicians']);
    Route::match(['put', 'patch'], '/services/{service}', [CatalogController::class, 'updateService']);
    Route::delete('/services/{service}', [CatalogController::class, 'destroyService']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);

    Route::match(
        ['put', 'patch'],
        '/bookings/{booking}/status',
        [BookingController::class, 'updateStatus']
    );

    Route::post('/bookings/{booking}/rating', [BookingController::class, 'rating']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}/payments', [InvoiceController::class, 'payments']);
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'submitPayment']);
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']);

    // Payment approvals
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve']);
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject']);
});


Route::get(
    '/notifications/unread-count',
    [NotificationController::class, 'unreadCount']
);
