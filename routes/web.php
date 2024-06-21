<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\FirebaseController::class, 'index'])->name('index');
Route::post('/', [App\Http\Controllers\FirebaseController::class, 'upload'])->name('upload');
Route::get('/delete', [App\Http\Controllers\FirebaseController::class, 'deleteFile'])->name('delete');
