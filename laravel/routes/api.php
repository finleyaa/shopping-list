<?php

use App\Http\Controllers\ItemController;
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

Route::apiResource('/items', ItemController::class)
    ->middleware('auth:sanctum');

/* Auth */
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
Route::get('/user', [App\Http\Controllers\AuthController::class, 'user'])
    ->middleware('auth:sanctum');
Route::patch('/user', [App\Http\Controllers\AuthController::class, 'update'])
    ->middleware('auth:sanctum');
