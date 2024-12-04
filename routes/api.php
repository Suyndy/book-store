<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsAdmin;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('verify', [AuthController::class, 'verify']);
Route::post('set-password', [AuthController::class, 'setPassword']);
Route::post('login', [AuthController::class, 'login']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-forgot-password', [AuthController::class, 'verifyForgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::middleware(['web'])->get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Route::middleware('web')->group(function () {
//     Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// });
// // Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth:api')->post('refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:api')->get('me', [AuthController::class, 'me']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'getAll']);
    Route::get('/{id}', [BookController::class, 'getOne']);

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::post('/', [BookController::class, 'store']);
        Route::put('/{id}', [BookController::class, 'update']);
        Route::delete('/{id}', [BookController::class, 'softDelete']);
    });
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'getAll']);
    Route::get('/{id}', [CategoryController::class, 'getOne']);

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'softDelete']);
    });
});

Route::get('/test-mail', function () {
    $email = 'cehadod439@kindomd.com';
    $verificationUrl = 'http://example.com/verify?token=12345';
    Mail::to($email)->send(new \App\Mail\VerifyEmail($verificationUrl)); 
    return 'Email sent!';
});