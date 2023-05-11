<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
Route::post('/settings/api/add', [App\Http\Controllers\APIConnectionController::class,'store'])->name('settings.api.add');
Route::post('/settings/api/{id}/update', [App\Http\Controllers\APIConnectionController::class, 'update'])->name('settings.api.update');

Route::get('notification', [App\Http\Controllers\SendNotification::class, 'create'])->name('notification.create');
Route::post('notification', [App\Http\Controllers\SendNotification::class, 'store'])->name('notification.store');
Route::get('/notifications/mark-as-read/{notification}', [App\Http\Controllers\SendNotification::class, 'markAsRead'])->name('notifications.markAsRead');
Route::get('/notifications/mark-un-read/{notification}', [App\Http\Controllers\SendNotification::class, 'markUnRead'])->name('notifications.markUnRead');
Route::get('/notifications/all', [App\Http\Controllers\SendNotification::class, 'showAll'])->name('notifications.all');
Route::get('/notifications/send', [App\Http\Controllers\SendNotification::class, 'send'])->name('notifications.create');

Route::get('/task', [App\Http\Controllers\TaskController::class, 'index'])->name('index');
Route::post('/task', [App\Http\Controllers\TaskController::class, 'store'])->name('store.task');
Route::delete('/task/{task}', [App\Http\Controllers\TaskController::class, 'delete'])->name('delete.task');
Route::resource('customers', App\Http\Controllers\KiotVietTo1OfficeController::class);
Route::get('customers/{id}/re_sync', [App\Http\Controllers\KiotVietTo1OfficeController::class, 're_sync']);
// Route::post('customers/hook', [App\Http\Controllers\KiotVietTo1OfficeController::class, 'hook']);

Route::post('/webhook', [App\Http\Controllers\KiotVietTo1OfficeController::class, 'handle']);
