<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PreprocessingController;

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

$match = ['GET', 'POST'];
Route::match($match, '/', [SearchController::class, 'index'])->name('home');
Route::get('/detail/{doc}', [SearchController::class, 'detail'])->name('detail');
Route::get('/documents', [PreprocessingController::class, 'index'])->name('documents');

