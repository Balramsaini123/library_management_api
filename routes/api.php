<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
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

const UUID_PLACEHOLDER = '{uuid}';

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [AuthController::class, 'usersList']);
    Route::prefix('users')->group(function () {
        Route::get(UUID_PLACEHOLDER, [AuthController::class, 'user']);
        Route::put(UUID_PLACEHOLDER, [AuthController::class, 'updateUser']);
        Route::delete(UUID_PLACEHOLDER, [AuthController::class, 'deleteUser']);
    });
    Route::post('book/store', [BookController::class, 'create']);
    Route::get('books/search', [BookController::class, 'readAll']);
    Route::put('book/update/{uuid}', [BookController::class, 'update']);
    Route::delete('book/delete/{uuid}', [BookController::class, 'delete']);
    Route::get('book/read/{uuid}', [BookController::class, 'read']);
    Route::post('/books/import', [BookController::class, 'importBooks']);
    Route::get('/books/export', [BookController::class, 'exportBooks']);
});
