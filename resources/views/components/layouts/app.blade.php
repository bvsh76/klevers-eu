@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @foreach (array_keys(config('locales.supported')) as $code)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ \App\Support\LocaleUrl::to($code) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ \App\Support\LocaleUrl::to(config('locales.default')) }}">
</head>
<body>
    <header>
        <x-language-switcher />
    </header>
    <main>
        {{ $slot }}
    </main>
</body>
</html>