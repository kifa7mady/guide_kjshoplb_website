<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GuideHomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/home', [GuideHomeController::class, 'home']);
Route::get('/categories', [GuideHomeController::class, 'categories']);
Route::get('/top-categories', [GuideHomeController::class, 'topCategories']);
Route::get('/customer-jobs', [GuideHomeController::class, 'customerJobs']);
Route::get('/customer-job/{id}', [GuideHomeController::class, 'customerJob']);
Route::get('/featured-stores', [GuideHomeController::class, 'featuredStores']);
Route::get('/regions', [GuideHomeController::class, 'regions']);
Route::get('/customers', [GuideHomeController::class, 'customers']);
