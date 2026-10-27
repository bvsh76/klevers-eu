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
        @foreach (\App\Support\LocaleUrl::alternates() as $code => $href)
            <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
        @endforeach
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