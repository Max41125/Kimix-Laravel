<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::get('/sanctum/csrf-cookie', function () {
   
    $controller = new CsrfCookieController();
    $response = $controller->show();

    // Дополнительная обработка ответа
    return response()->json([
        'message' => 'CSRF token set',
        'data' => json_decode($response->getContent()), // Возвращаем тело ответа
    ], 200);
})->middleware('web');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
// Маршрут для подтверждения электронной почты
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');