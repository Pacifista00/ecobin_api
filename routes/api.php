<?php

use App\Http\Controllers\BinController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\UserController;
use App\Models\History;
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
Route::get('/logged-user', [App\Http\Controllers\AuthController::class, 'loggedUser'])->middleware('auth:sanctum');
Route::post('/user/update', [App\Http\Controllers\AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword'])->middleware('auth:sanctum');


Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
Route::post('/user/add', [App\Http\Controllers\UserController::class, 'storeUser'])->middleware('auth:sanctum');
Route::post('/user/update/{id}', [App\Http\Controllers\UserController::class, 'updateUser'])->middleware('auth:sanctum');
Route::delete('/user/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUser'])->middleware('auth:sanctum');



// Ambil semua bins
Route::get('/bins', [BinController::class, 'index']);
Route::post('/bin/add', [BinController::class, 'store']);
Route::get('/bin/{id}', [BinController::class, 'show']);
Route::post('/bin/update/{id}', [BinController::class, 'update']);
Route::delete('/bin/delete/{id}', [BinController::class, 'destroy']);

Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index']);
Route::get('/tes-notif', [App\Http\Controllers\NotificationController::class, 'test']);
Route::get('/histories', [App\Http\Controllers\HistoryController::class, 'index']);

Route::get('/bin-sensors', [SensorController::class, 'bins']);
Route::post('/sensor/{token}', [SensorController::class, 'store']);

Route::get('/empty-bin/{id}/{type}', [HistoryController::class, 'emptyBin']);

// routes/api.php
Route::post('/save-fcm-token', [UserController::class, 'saveFcmToken'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
