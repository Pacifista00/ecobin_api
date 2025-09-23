<?php

use App\Http\Controllers\BinController;
use Illuminate\Http\Request;
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

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');


// Ambil semua bins
Route::get('/bins', [BinController::class, 'index']);
Route::post('/bin/add', [BinController::class, 'store']);
Route::get('/bin/{id}', [BinController::class, 'show']);
Route::put('/bin/update/{id}', [BinController::class, 'update']);
Route::delete('/bin/delete/{id}', [BinController::class, 'destroy']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
