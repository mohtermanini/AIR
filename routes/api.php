<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\IRController;
use App\Http\Controllers\TermController;
use App\IR\TermsWeight;
use App\IR\VectorModel;

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

Route::prefix("document")->group(function () {
    Route::get("/", [DocumentController::class, "index"]);
    Route::post("/{lang}", [DocumentController::class, "store"]);
    Route::put("{/document}/{lang}", [DocumentController::class, "update"]);
    Route::delete("/{document}", [DocumentController::class, "destroy"]);
});

Route::post("boolean-model/{lang}", [IRController::class, "booleanModel"]);
Route::post("extended-boolean-model/{lang}", [IRController::class, "extendedBooleanModel"]);
Route::post("vector-model/{lang}", [IRController::class, "vectorModel"]);