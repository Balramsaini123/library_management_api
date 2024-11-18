<?php

use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/payment/overdue/{book_id}/{user_id}', [StripePaymentController::class, 'overduePayment'])->name('overdue.payment');
Route::post('/payment/overdue/process', [StripePaymentController::class, 'processOverduePayment'])->name('overdue.payment.process');
