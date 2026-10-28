@props([
    'title' => config('app.name'),
    'description' => null,
    'alternates' => true,
])
@php($nav = \App\Support\Navigation::for(app()->getLocale()))
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
    @if ($ga4 = \App\Models\Setting::get('ga4_id'))
        <meta name="ga4-id" content="{{ $ga4 }}">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-md focus:bg-primary focus:px-4 focus:py-2 focus:text-white">{{ __('site.skip_to_content') }}</a>
    <x-site.header :nav="$nav" />
    <main id="main" class="flex-1">
        {{ $slot }}
    </main>
    <x-site.footer :nav="$nav" />
    <x-site.cookie-banner />
</body>
</html>