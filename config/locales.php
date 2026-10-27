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

    /*
    | Translated URL prefixes of sections: /{locale}/{prefix}/{slug}.
    | Pages without a section live at /{locale}/{slug}.
    | Adding a language requires a prefix for every section.
    */
    'sections' => [
        'services' => ['ru' => 'uslugi', 'et' => 'teenused', 'en' => 'services'],
        'segments' => ['ru' => 'klientam', 'et' => 'klientidele', 'en' => 'clients'],
    ],

];
