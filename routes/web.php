<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('invoiceList');
    })->name('home');

    Route::resources([
        'customers' => CustomerController::class,
        'products' => ProductController::class,
        'inventory' => InventoryController::class,
    ]);

    Route::get('/inventoryhistory', [InventoryController::class, 'inventoryHistory'])->name('inventoryHistory');

    Route::group(['prefix' => 'invoice'], function () {
        Route::get('/list', [InvoiceController::class, 'invoiceList'])->name('invoiceList');
        Route::get('/form', [InvoiceController::class, 'showGenerateForm'])->name('showGenerateForm');
        Route::post('/generate', [InvoiceController::class, 'generateInvoice'])->name('generateInvoice');
        Route::post('/add-product', [InvoiceController::class, 'addSingleProduct'])->name('addSingleProduct');
        Route::post('/product-info', [InvoiceController::class, 'getProductInfo'])->name('getProductInfo');
        Route::get('/download-zip/{file}', [InvoiceController::class, 'downloadZip'])->name('downloadZip');
        Route::get('/download-sample', [InvoiceController::class, 'downloadSample'])->name('downloadSample');
        Route::post('/gen-inv', [InvoiceController::class, 'genInv'])->name('geninv');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('/export', [CustomerController::class, 'exportCustomers'])->name('exportCustomers');
        Route::get('/exportDownload/{file}', [CustomerController::class, 'exportDownload'])->name('exportDownload');
        Route::post('/import', [CustomerController::class, 'importCustomers'])->name('importCustomers');
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('/export', [ProductController::class, 'exportProducts'])->name('exportProducts');
        Route::post('/import', [ProductController::class, 'importProducts'])->name('importProducts');
    });

    Route::group(['prefix' => 'invntry'], function () {
        Route::get('/export', [InventoryController::class, 'exportInventory'])->name('exportInventory');
        Route::post('/import', [InventoryController::class, 'importInventory'])->name('importInventory');
    });
});
