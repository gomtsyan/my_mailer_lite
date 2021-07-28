<?php

use App\Http\Controllers\Api\V1\SecurityController;
use App\Http\Controllers\Api\V1\SubscriberController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1', 'as' => 'api'], function () {
    Route::get('check/key', [SecurityController::class, 'checkApiKey'])->name('api.v1.security.checkApiKey');
    Route::get('subscribers', [SubscriberController::class, 'list'])->name('api.v1.subscriber.list');
    Route::get('subscribers/add', [SubscriberController::class, 'store'])->name('api.v1.subscriber.store');
    Route::get('subscribers/{id}/delete', [SubscriberController::class, 'destroy'])->name('api.v1.subscriber.destroy');
    Route::get('subscribers/{id}/edit', [SubscriberController::class, 'update'])->name('api.v1.subscriber.update');
});
