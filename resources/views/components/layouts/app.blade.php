@props([
    'title' => config('app.name'),
    'description' => null,
    'alternates' => true,
])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif
    @if ($alternates)
        <link rel="canonical" href="{{ url()->current() }}">
        @foreach (array_keys(config('locales.supported')) as $code)
            <link rel="alternate" hreflang="{{ $code }}" href="{{ \App\Support\LocaleUrl::to($code, false) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ \App\Support\LocaleUrl::to(config('locales.default'), false) }}">
    @endif
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
