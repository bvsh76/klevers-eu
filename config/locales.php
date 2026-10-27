<?php

return [

    /*
    | Supported site locales: URL prefix => native name.
    | Order defines the order in the language switcher.
    | To add a language: add a line here and create lang/{code}.
    */

    // Fixed default locale; config('app.locale') changes per request.
    'default' => env('APP_LOCALE', 'ru'),

    'supported' => [
        'ru' => 'Русский',
        'et' => 'Eesti',
        'en' => 'English',
    ],

];
