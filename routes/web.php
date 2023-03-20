<?php

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

// Route::get('/', function () {
//     return view('admin.home');
// });

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/settings/api', [App\Http\Controllers\HomeController::class, 'settings_api'])->name('settings.api');
// Route::post('/settings/api/add', [App\Http\Controllers\APIConnectionController::class,'store'])->name('settings.api.add');
Route::post('/settings/api/{id}/update', [App\Http\Controllers\APIConnectionController::class, 'update'])->name('settings.api.update');

