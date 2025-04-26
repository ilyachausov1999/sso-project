<?php

use App\Http\News\SomethingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::prefix('something')
        ->name('something.')
        ->controller(SomethingController::class)
        ->group(function () {
            Route::get('/', 'getSomething')->name('getSomething');
        });
});
