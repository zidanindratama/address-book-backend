<?php

use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/contact", [ContactController::class, "index"]);
Route::post("/contact/upload", [ContactController::class, "upload"]);
Route::get("/contact/{id}", [ContactController::class, "show"]);
Route::post("/contact", [ContactController::class, "store"]);
Route::put("/contact/{id}", [ContactController::class, "update"]);
Route::delete("/contact/{id}", [ContactController::class, "destroy"]);
