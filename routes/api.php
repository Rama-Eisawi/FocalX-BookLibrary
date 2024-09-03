<?php

use App\Http\Controllers\{AuthController, BookController, BorrowRecordController, UserController};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('book', BookController::class);

Route::apiResource('user', UserController::class);

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('register', 'register')->name('auth.register');
        Route::post('login', 'login')->name('auth.login');
        Route::post('logout', 'logout')->name('auth.logout')->middleware('auth:api'); //This middleware ensures that the user is authenticated via a JWT token
    });


Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('borrow', BorrowRecordController::class);
    Route::post('borrow-records/{id}/return', [BorrowRecordController::class, 'returnBook']);
});
