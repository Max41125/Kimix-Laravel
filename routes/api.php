<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChemicalController;

Route::get('/chemicals/search', [ChemicalController::class, 'search']);
Route::get('/chemicals', [ChemicalController::class, 'index']);
Route::get('/chemicals/{id}', [ChemicalController::class, 'show']);
Route::post('/chemicals', [ChemicalController::class, 'store']);
Route::put('/chemicals/{id}', [ChemicalController::class, 'update']);
Route::delete('/chemicals/{id}', [ChemicalController::class, 'destroy']);
