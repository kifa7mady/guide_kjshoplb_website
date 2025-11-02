<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Guide\GuideFrontController;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return redirect()->route('guide.index');
});




// Define a route with a prefix
Route::prefix('guide')->group(function () {
    Route::get('/', [GuideFrontController::class, 'index'])->name('guide.index');
    Route::get('/get-home-page', [GuideFrontController::class, 'getHomePage'])->name('guide.homepage');


    Route::get('/search', [GuideFrontController::class, 'search'])->name('search.show');
    Route::get('/{id}/{slug?}', [GuideFrontController::class, 'showCustomer'])->name('customer.show');
    Route::get('/region/{name?}/{id}/', [GuideFrontController::class, 'showCategories'])->name('category.show');
    Route::get('/{name?}/{id}/{slug?}', [GuideFrontController::class, 'showSubCategories'])->name('subCategory.show');
    Route::get('/{name?}/{parent_id}/{slug?}/{id}', [GuideFrontController::class, 'showCustomerJobs'])->name('showCustomerJobs.show');
//    Route::get('/sub-category/{id}/{slug?}', [GuideFrontController::class, 'showSubCategories'])->name('subcategory.show');
});



