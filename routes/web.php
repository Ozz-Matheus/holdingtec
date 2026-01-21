<?php

use App\Support\AppHelper;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(AppHelper::getHomeUrl());
})->name('home');
