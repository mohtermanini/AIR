<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Web\DocumentController;

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

Route::get('/', [FrontendController::class, "index"])->name("home");

Route::resource('documents', DocumentController::class)->except(["index"]);
Route::get('search/documents', [DocumentController::class, "search"])->name("documents.search");

Route::get('/about', [FrontendController::class, "about"])->name("about");