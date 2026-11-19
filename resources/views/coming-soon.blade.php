<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }} — {{ __('site.coming_soon_title') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-surface text-ink antialiased">
    <main class="mx-auto flex min-h-screen max-w-xl flex-col items-center justify-center gap-6 px-6 text-center">
        <p class="text-2xl font-semibold text-primary">{{ config('app.name') }}</p>
        <h1 class="text-3xl font-bold sm:text-4xl">{{ __('site.coming_soon_title') }}</h1>
        <p class="text-lg text-muted">{{ __('site.coming_soon_text') }}</p>
        @if ($whatsapp || $telegram)
            <p class="text-muted">{{ __('site.coming_soon_contact') }}</p>
            <div class="flex flex-wrap justify-center gap-3">
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="rounded-card bg-primary px-5 py-3 font-medium text-white hover:bg-primary-hover">WhatsApp</a>
                @endif
                @if ($telegram)
                    <a href="https://t.me/{{ $telegram }}" target="_blank" rel="noopener" class="rounded-card bg-primary px-5 py-3 font-medium text-white hover:bg-primary-hover">Telegram</a>
                @endif
            </div>
        @endif
    </main>
</body>
</html>
