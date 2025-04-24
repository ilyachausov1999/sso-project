<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')

    ->group(function () {
    Route::name('auth.')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('/register', 'register')->name('register');
            Route::post('/login', 'login')->name('login');
            Route::post('/refresh', 'refresh')->name('refresh');

            Route::get('/auth/google-redirect', 'googleRedirect')->name('googleRedirect');
            Route::get('/auth/google-callback', 'googleCallback')->name('googleCallback');
        });
});
