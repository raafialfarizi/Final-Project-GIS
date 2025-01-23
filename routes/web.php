<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SumselController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/choropleth_map', [SumselController::class, 'index']);