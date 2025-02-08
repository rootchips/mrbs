<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return response()->json([
        'API' => 'v1.0',
        'Platform' => 'Meeting Room Booking System'
    ]);
});
