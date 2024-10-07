<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Главная страница
Route::get('/', function () {
    return view('welcome');
});

// Маршрут для отправки письма с подтверждением
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // Это страница, которая говорит пользователю проверить почту
})->middleware('auth')->name('verification.notice');

// Маршрут для обработки ссылки из письма
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Подтверждение email

    // Редирект на фронтенд после успешной верификации
    return redirect(env('FRONTEND_URL') . '/verification-success');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Маршрут для повторной отправки письма
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
