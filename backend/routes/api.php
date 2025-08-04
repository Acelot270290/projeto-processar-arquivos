<?php

use App\Http\Controllers\ProcessedRecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('processed-records')->group(function () {
    Route::post('/', [ProcessedRecordController::class, 'store']);
});
