<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Существующие маршруты
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Маршруты для профиля, доступные только авторизованным пользователям
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Добавляем маршруты для админки с middleware проверки на администратора
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    // Здесь можно добавить другие маршруты админки
});

// Подключаем маршруты для аутентификации
require __DIR__.'/auth.php';