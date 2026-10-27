<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => config('locales.default')]))
    ->name('root');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('locales.supported')))])
    ->middleware('locale')
    ->group(function () {
        Route::view('/', 'home')->name('home');
    });
