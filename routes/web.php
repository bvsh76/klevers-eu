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

Route::get('robots.txt', function () {
    $lines = config('seo.indexable')
        ? ['User-agent: *', 'Disallow:', '', 'Sitemap: '.url('sitemap.xml')]
        : ['User-agent: *', 'Disallow: /'];

    return response(implode("\n", $lines)."\n")
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

Route::get('sitemap.xml', function () {
    // Localized static pages. C3: add pages from the database.
    $routeNames = ['home'];
    $locales = array_keys(config('locales.supported'));

    $urls = [];
    foreach ($routeNames as $name) {
        $alternates = [];
        foreach ($locales as $locale) {
            $alternates[$locale] = route($name, ['locale' => $locale]);
        }
        $alternates['x-default'] = route($name, ['locale' => config('locales.default')]);

        foreach ($locales as $locale) {
            $urls[] = ['loc' => $alternates[$locale], 'alternates' => $alternates];
        }
    }

    return response('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.view('sitemap', ['urls' => $urls])->render())
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');
