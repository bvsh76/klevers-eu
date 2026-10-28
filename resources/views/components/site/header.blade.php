@props(['nav'])
@php
    $groups = collect([
        ['label' => __('site.nav_services'), 'items' => $nav['services']],
        ['label' => __('site.nav_clients'), 'items' => $nav['segments']],
    ])->filter(fn ($group) => $group['items']->isNotEmpty());
    $links = collect(['about', 'contacts'])->map(fn ($key) => $nav['pages']->get($key))->filter();
    $contact = $nav['pages']->get('contacts');
@endphp
<header x-data="{ open: false }" @keydown.escape.window="open = false" class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-site items-center justify-between gap-6 px-4 sm:px-6">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-xl font-bold tracking-tight text-primary">{{ config('app.name') }}</a>

        <nav aria-label="{{ __('site.main_nav') }}" class="hidden items-center gap-1 lg:flex">
            @foreach ($groups as $group)
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button type="button" @click="open = !open" :aria-expanded="open" class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium hover:bg-surface-alt">
                        {{ $group['label'] }}
                        <svg class="size-4 text-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-cloak x-show="open" x-transition class="absolute left-0 mt-2 w-72 rounded-card border border-line bg-surface p-2 shadow-lg">
                        @foreach ($group['items'] as $item)
                            <a href="{{ $item['url'] }}" class="block rounded-md px-3 py-2 text-sm hover:bg-surface-alt">{{ $item['title'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-surface-alt">{{ $link['title'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <x-site.messengers class="hidden lg:flex" />
            <x-language-switcher class="hidden lg:block" />
            @if ($contact)
                <a href="{{ $contact['url'] }}" class="hidden rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover sm:inline-flex">{{ __('site.cta_consultation') }}</a>
            @endif
            <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-menu" class="rounded-md p-2 hover:bg-surface-alt lg:hidden">
                <span class="sr-only" x-text="open ? @js(__('site.menu_close')) : @js(__('site.menu'))">{{ __('site.menu') }}</span>
                <svg x-show="!open" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                <svg x-cloak x-show="open" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" x-cloak x-show="open" x-transition.opacity class="max-h-[calc(100dvh-4rem)] overflow-y-auto overscroll-contain border-t border-line bg-surface lg:hidden">
        <div class="mx-auto max-w-site space-y-6 px-4 py-6 sm:px-6">
            @foreach ($groups as $group)
                <div>
                    <p class="mb-2 px-3 text-xs font-semibold tracking-wide text-muted uppercase">{{ $group['label'] }}</p>
                    <ul class="space-y-1">
                        @foreach ($group['items'] as $item)
                            <li><a href="{{ $item['url'] }}" class="block rounded-md px-3 py-2 hover:bg-surface-alt">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            @if ($links->isNotEmpty())
                <ul class="space-y-1 border-t border-line pt-4">
                    @foreach ($links as $link)
                        <li><a href="{{ $link['url'] }}" class="block rounded-md px-3 py-2 font-medium hover:bg-surface-alt">{{ $link['title'] }}</a></li>
                    @endforeach
                </ul>
            @endif
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-line pt-4">
                <x-site.messengers class="-ml-2" />
                <x-language-switcher />
                @if ($contact)
                    <a href="{{ $contact['url'] }}" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">{{ __('site.cta_consultation') }}</a>
                @endif
            </div>
        </div>
    </div>
</header>