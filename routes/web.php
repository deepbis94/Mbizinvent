<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('customers.index');
    })->name('home');

    Route::resource('customers', CustomerController::class);

    Route::group(['prefix' => 'customer'], function () {
        Route::get('/export', [CustomerController::class, 'exportCustomers'])->name('exportCustomers');
        Route::get('/exportDownload/{file}', [CustomerController::class, 'exportDownload'])->name('exportDownload');
        Route::post('/import', [CustomerController::class, 'importCustomers'])->name('importCustomers');
    });
});
