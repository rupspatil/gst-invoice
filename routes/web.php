<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyImportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Customers CRUD
    Route::resource('customers', CustomerController::class);
    Route::get('/gst-lookup/{gst}', [CustomerController::class, 'gstLookup']);

    // Invoices
    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Items (Products & Services)
    Route::resource('items', ItemController::class);
    // Route::get('products', [ItemController::class, 'index'])->name('products.index');
    Route::get('/items/{id}/details', [ItemController::class, 'getDetails'])->name('items.details');
    Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

    //Product scrapping
    Route::get('/import/shopify', function () {
    return view('products.import');
    Route::post('/import/shopify', [ShopifyImportController::class, 'import'])->name('products.import');
});
    

});

require __DIR__.'/auth.php';
