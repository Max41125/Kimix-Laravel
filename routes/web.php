<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Auth\CustomCsrfCookieController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Auth\BroadcastAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);




Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
// Маршрут для подтверждения электронной почты
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');


Route::post('/broadcasting/auth', [BroadcastAuthController::class, 'authorize']);