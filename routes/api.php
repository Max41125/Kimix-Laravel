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
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
    Route::post('/update-password', [PasswordResetController::class, 'updatePassword']);


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
    Route::get('/user/order/{orderId}', [OrderController::class, 'getUserOrder']);
    Route::get('/seller/{sellerId}/orders', [OrderController::class, 'getSellerOrders']);
    Route::get('/orders/{orderId}/status', [OrderController::class, 'getOrderStatus']); 
    Route::patch('/orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
    Route::get('/seller/{sellerId}', [SellerController::class, 'show']); // Получить данные продавца
    Route::post('/seller', [SellerController::class, 'updateSellerInfo']);
    Route::post('/user-address', [UserAddressController::class, 'storeOrUpdate']); // Сохранить или обновить
    Route::get('/user-address/{userId}', [UserAddressController::class, 'getByUserId']); // Получить по ID
    Route::delete('/user-address/{userId}', [UserAddressController::class, 'deleteByUserId']); // Удалить по ID
    Route::post('/contract-orders', [OrderController::class, 'createContractOrder']);
    Route::get('/contract-orders/{orderId}', [OrderController::class, 'getContractOrder']);

   
});


Route::get('/chemicals/search', [ChemicalController::class, 'search']);
Route::get('/chemicals', [ChemicalController::class, 'index']);
Route::get('/chemicals/{id}', [ChemicalController::class, 'show']);
Route::post('/chemicals', [ChemicalController::class, 'store']);
Route::put('/chemicals/{id}', [ChemicalController::class, 'update']);
Route::delete('/chemicals/{id}', [ChemicalController::class, 'destroy']);