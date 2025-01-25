<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SumselController;
use App\Http\Controllers\Sumsel1Controller;

Route::get('/', function () {
    return view('profilesumsel');
});

Route::get('/jumlah_restoran', [SumselController::class, 'Resto']);

Route::get('/populasi', [SumselController::class, 'index']);

Route::get('/ekonomi', [SumselController::class, 'GDP']);

Route::get('/beragama_islam', [SumselController::class, 'beragama_islam']);

Route::get('/jumlah_kejahatan', [SumselController::class, 'jumlah_kejahatan']);

Route::get('/sumsel' ,[SumselController::class, 'index'])->name('sumsel');