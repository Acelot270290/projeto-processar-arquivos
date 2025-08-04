<?php

use App\Http\Controllers\ProcessedRecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/files', [ProcessedRecordController::class, 'listarArquivos']);
Route::get('/files/{id}', [ProcessedRecordController::class, 'mostrarArquivo']);

Route::prefix('processed-records')->group(function () {
    Route::post('/', [ProcessedRecordController::class, 'store']);
});
