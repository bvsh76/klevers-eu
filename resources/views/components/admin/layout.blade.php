@props(['title' => 'Админка'])
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    @stack('scripts')
</head>
<body>
@auth
    @php
        $menu = [
            'admin.dashboard' => 'Дашборд',
            'admin.pages.index' => 'Страницы',
            'admin.segments.index' => 'Сегменты',
            'admin.services.index' => 'Услуги',
            'admin.packages.index' => 'Пакеты',
            'admin.leads.index' => 'Заявки',
            'admin.subscribers.index' => 'Подписчики',
            'admin.settings.edit' => 'Настройки',
        ];
    @endphp
    <header class="topbar">
        <strong>{{ config('app.name') }} · админка</strong>
        <nav>
            @foreach ($menu as $route => $label)
                @if (Route::has($route) && ($route !== 'admin.settings.edit' || auth()->user()->role === \App\Enums\Role::Admin))
                    <a href="{{ route($route) }}" @class(['active' => request()->routeIs(str_replace('.index', '.*', $route))])>{{ $label }}</a>
                @endif
            @endforeach
        </nav>
        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="link">Выйти</button>
        </form>
    </header>
@endauth
<main class="container">
    @if (session('status'))
        <div class="alert success">{{ session('status') }}</div>
    @endif
    {{ $slot }}
</main>
</body>
</html>