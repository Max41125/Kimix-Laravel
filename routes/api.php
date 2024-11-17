<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChemicalController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ChatController;
use App\Http\Controllers\YourProtectedController;
use App\Http\Controllers\Api\SellerController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/chemicals/{id}/suppliers', [ChemicalController::class, 'getSuppliersByChemicalId']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages/{orderId}', [ChatController::class, 'getMessages']);
    Route::post('/chat/upload', [ChatController::class, 'uploadDocument']);
    Route::get('/chat/documents/{orderId}', [ChatController::class, 'getDocuments']);
});




Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/users/{userId}/products', [OrderController::class, 'getUserProducts']);
    Route::put('/users/{userId}/products', [OrderController::class, 'updateProducts']);
    Route::delete('/users/{userId}/products', [OrderController::class, 'removeProducts']);
    Route::get('/protected-route', [YourProtectedController::class, 'index']);
    Route::get('/user/{userId}/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/seller/{sellerId}/orders', [OrderController::class, 'getSellerOrders']);
    Route::get('/orders/{orderId}/status', [OrderController::class, 'getOrderStatus']); 
    Route::patch('/orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
    Route::get('/seller', [SellerController::class, 'show']);
    Route::post('/seller', [SellerController::class, 'store']);
    Route::put('/seller', [SellerController::class, 'update']);




   
});


Route::get('/chemicals/search', [ChemicalController::class, 'search']);
Route::get('/chemicals', [ChemicalController::class, 'index']);
Route::get('/chemicals/{id}', [ChemicalController::class, 'show']);
Route::post('/chemicals', [ChemicalController::class, 'store']);
Route::put('/chemicals/{id}', [ChemicalController::class, 'update']);
Route::delete('/chemicals/{id}', [ChemicalController::class, 'destroy']);