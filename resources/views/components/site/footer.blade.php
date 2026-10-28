@props(['nav'])
@php
    $columns = collect([
        ['label' => __('site.nav_services'), 'items' => $nav['services']],
        ['label' => __('site.nav_clients'), 'items' => $nav['segments']],
        ['label' => __('site.nav_company'), 'items' => collect(['about', 'contacts', 'privacy'])->map(fn ($key) => $nav['pages']->get($key))->filter()],
    ])->filter(fn ($column) => $column['items']->isNotEmpty());
@endphp
<footer class="mt-16 border-t border-line bg-surface-alt">
    <div class="mx-auto grid max-w-site gap-8 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
        <div class="space-y-4">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-xl font-bold tracking-tight text-primary">{{ config('app.name') }}</a>
            <x-language-switcher />
        </div>
        @foreach ($columns as $column)
            <div>
                <p class="mb-3 text-sm font-semibold">{{ $column['label'] }}</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($column['items'] as $item)
                        <li><a href="{{ $item['url'] }}" class="text-muted hover:text-ink">{{ $item['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <div class="border-t border-line">
        <div class="mx-auto max-w-site px-4 py-8 sm:px-6">
            <x-site.subscribe-form />
        </div>
    </div>
    <div class="border-t border-line">
        <p class="mx-auto max-w-site px-4 py-6 text-sm text-muted sm:px-6">&copy; {{ now(config('app.display_timezone'))->year }} {{ config('app.name') }}. {{ __('site.footer_rights') }}</p>
    </div>
</footer>