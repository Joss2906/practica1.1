<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VerificationCodeController;
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
    // return view('login');
    return redirect()->route('register');
});


Route::post('auth', [LoginController::class, 'login'])->name('auth');
Route::get('auth', [LoginController::class, 'loginForm'])->name('auth');

Route::get('register', [LoginController::class, 'registerForm'])->name('register');
Route::post('register', [LoginController::class, 'create'])->name('register');

Route::get('verify', [VerificationCodeController::class, 'verifyCodeView'])->name('verify');
Route::post('verify', [VerificationCodeController::class, 'validateCode'])->name('verify');

Route::middleware(['auth'])->group(function () {
    Route::get('welcome', [LoginController::class, 'welcome'])->name('welcome');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});