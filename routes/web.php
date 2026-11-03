<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::post('/posture-review', [ReviewController::class, 'store'])->name('review.store');
