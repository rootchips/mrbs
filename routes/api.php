<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, 
    UserController, 
    RoomController,
    SettingController,
    ChatBotController,
    BookingController
};

Route::get('/', function () {
    return response()->json([
        'API' => 'v1.0',
        'Platform' => 'Meeting Room Booking System'
    ]);
});


Route::controller(RoomController::class)->group(function () {
    Route::get('/rooms', 'portal');
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/bookings/{room}', 'getTopThreeBookingForToday');
});

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'authenticated');
        Route::post('/verify', 'verify');
        Route::get('google', 'redirectToGoogle');
        Route::get('google/callback', 'handleGoogleCallback');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('reset-password', 'resetPassword');
        Route::post('/verify', 'verify');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('/register', 'store');
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/user', 'whois');
            Route::post('/logout', 'logout');
            Route::post('/change-password', 'changePassword');
            Route::post('/resend-verification-code', 'resendEmailVerification');
            Route::post('/verify-account', 'verifyAccount');
            Route::post('change-password', 'changePassword');
            Route::post('update-profile/{user}', 'updateProfile');
        });
    });

    Route::post('/chat', [ChatBotController::class, 'chat']);
    Route::post('/chat-clear', [ChatBotController::class, 'clearMessage']);

    Route::prefix('internal')->group(function () {
        Route::prefix('users')->group(function () {
            Route::controller(UserController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('{user}', 'show');
                    Route::post('{user}', 'update');
                    Route::delete('{user}', 'destroy');
                    Route::post('/check-email', 'checkEmail');
            });
        });

        Route::prefix('bookings')->group(function () {
            Route::controller(BookingController::class)->group(function () {
                Route::get('/all', 'all');
                Route::get('/show/{booking}', 'show');
                Route::get('/get-disabled-dates', 'getDisabledDates');
                Route::post('/store', 'store');
                Route::get('list-by-date/{booking}', 'listByDate');
                Route::post('/cancel/{booking}', 'cancel');
            });
        });

        Route::prefix('rooms')->group(function () {
            Route::controller(RoomController::class)->group(function () {
                    Route::post('/update/{room}', 'update');
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::get('{room}', 'show');
                    Route::delete('{room}', 'destroy');
            });
        });

        Route::prefix('settings')->group(function () {
            Route::controller(SettingController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/store', 'store');
            });
        });
    });
});