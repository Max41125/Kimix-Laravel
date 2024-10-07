<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChemicalController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\VerificationController; // Импортируйте контроллер верификации
use App\Http\Controllers\YourProtectedController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    
    // Маршрут для отправки письма с подтверждением email
    Route::get('/email/verification-notification', [RegisteredUserController::class, 'sendVerificationEmail'])
        ->middleware('auth:sanctum')
        ->name('verification.send');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');


    Route::get('/protected-route', [YourProtectedController::class, 'index']);
    Route::get('/chemicals/search', [ChemicalController::class, 'search']);
    Route::get('/chemicals', [ChemicalController::class, 'index']);
    Route::get('/chemicals/{id}', [ChemicalController::class, 'show']);
    Route::post('/chemicals', [ChemicalController::class, 'store']);
    Route::put('/chemicals/{id}', [ChemicalController::class, 'update']);
    Route::delete('/chemicals/{id}', [ChemicalController::class, 'destroy']);
});
