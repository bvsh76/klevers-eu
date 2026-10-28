<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SubscriberController;
use App\Http\Middleware\StoreUtm;
use App\Support\Sitemap;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => config('locales.default')]))
    ->name('root');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('locales.supported')))])
    ->middleware(['locale', StoreUtm::class])
    ->group(function () {
        $slug = '[a-z0-9]+(?:-[a-z0-9]+)*';
        $sections = collect(config('locales.sections'))->flatten()->unique()->implode('|');

        Route::get('/', [ContentController::class, 'home'])->name('home');
        Route::post('lead', [LeadController::class, 'store'])->middleware('throttle:5,1')->name('lead.store');
        Route::post('subscribe', [SubscriberController::class, 'store'])->middleware('throttle:5,1')->name('subscribe');
        Route::get('subscribe/confirm/{subscriber}', [SubscriberController::class, 'confirm'])->whereNumber('subscriber')->middleware('signed')->name('subscribe.confirm');
        Route::get('unsubscribe/{token}', [SubscriberController::class, 'unsubscribeForm'])->where('token', '[A-Za-z0-9]{48}')->name('unsubscribe');
        Route::post('unsubscribe/{token}', [SubscriberController::class, 'unsubscribe'])->where('token', '[A-Za-z0-9]{48}')->name('unsubscribe.store');
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
