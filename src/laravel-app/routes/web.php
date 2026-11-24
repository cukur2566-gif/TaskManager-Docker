<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoTaskController;

Route::get('/go-tasks', [GoTaskController::class, 'index'])
    ->name('go-tasks.index');

Route::post('/go-tasks', [GoTaskController::class, 'store'])
    ->name('go-tasks.store');

Route::delete('/go-tasks/{id}', [GoTaskController::class, 'destroy'])
    ->name('go-tasks.destroy');

Route::get('/', function () {
    return view('welcome');
});
