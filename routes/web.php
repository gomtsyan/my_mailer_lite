<?php

use App\Http\Controllers\CheckKeyController;
use App\Http\Controllers\SubscriberManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [CheckKeyController::class, 'check'])->name('checkKey');
Route::get('subscribers', [SubscriberManagementController::class, 'index'])->name('subscribers.index');
Route::get('subscribers/create', [SubscriberManagementController::class, 'create'])->name('subscribers.create');
Route::get('subscribers/{id}/edit', [SubscriberManagementController::class, 'edit'])->name('subscribers.edit');

