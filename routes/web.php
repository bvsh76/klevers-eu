<?php

use App\Http\Controllers\ContentController;
use App\Support\Sitemap;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => config('locales.default')]))
    ->name('root');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('locales.supported')))])
    ->middleware('locale')
    ->group(function () {
        $slug = '[a-z0-9]+(?:-[a-z0-9]+)*';
        $sections = collect(config('locales.sections'))->flatten()->unique()->implode('|');

        Route::get('/', [ContentController::class, 'home'])->name('home');
        Route::get('{section}/{slug}', [ContentController::class, 'section'])
            ->where(['section' => $sections, 'slug' => $slug])
            ->name('section');
        Route::get('{slug}', [ContentController::class, 'page'])
            ->where(['slug' => $slug])
            ->name('page');
    });

Route::get('robots.txt', function () {
    $lines = config('seo.indexable')
        ? ['User-agent: *', 'Disallow:', '', 'Sitemap: '.url('sitemap.xml')]
        : ['User-agent: *', 'Disallow: /'];

    return response(implode("\n", $lines)."\n")
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

Route::get('sitemap.xml', function () {
    return response('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.view('sitemap', ['urls' => Sitemap::urls()])->render())
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

require __DIR__.'/admin.php';
