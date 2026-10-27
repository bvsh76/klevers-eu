@php
    $t = fn ($model, string $field) => $model->getTranslation($field, $locale, false);
    $title = $page ? $t($page, 'title') : config('app.name');
@endphp
<x-layouts.app :title="$page ? ($t($page, 'meta_title') ?: $title) : $title" :description="$page ? ($t($page, 'meta_description') ?: null) : null">
    <section class="bg-surface-alt">
        <div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:py-24">
            <h1 class="max-w-3xl text-4xl font-bold tracking-tight text-primary sm:text-5xl">{{ $title }}</h1>
            @if ($page && filled($t($page, 'intro')))
                <p class="mt-6 max-w-2xl text-lg text-muted">{{ $t($page, 'intro') }}</p>
            @endif
            <div class="mt-8 flex flex-wrap gap-3">
                @if ($contactUrl)
                    <a href="{{ $contactUrl }}" class="rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.cta_consultation') }}</a>
                @endif
                @if ($services->isNotEmpty())
                    <a href="#services" class="rounded-md border border-line bg-surface px-5 py-3 font-semibold hover:border-primary hover:text-primary">{{ __('site.nav_services') }}</a>
                @endif
            </div>
        </div>
    </section>

    @if ($services->isNotEmpty())
        <section id="services" class="mx-auto max-w-site scroll-mt-20 px-4 py-16 sm:px-6">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('site.nav_services') }}</h2>
            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                @foreach ($services as $service)
                    <a href="{{ $service->localizedUrl($locale) }}" class="group flex flex-col rounded-card border border-line bg-surface p-6 transition hover:border-primary hover:shadow-md">
                        <h3 class="text-lg font-semibold group-hover:text-primary">{{ $t($service, 'title') }}</h3>
                        @if (filled($t($service, 'intro')))
                            <p class="mt-2 flex-1 text-muted">{{ $t($service, 'intro') }}</p>
                        @endif
                        <span class="mt-4 text-sm font-semibold text-primary">{{ __('site.more') }} &rarr;</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($segments->isNotEmpty())
        <section class="bg-surface-alt">
            <div class="mx-auto max-w-site px-4 py-16 sm:px-6">
                <h2 class="text-3xl font-bold tracking-tight">{{ __('site.home_segments_title') }}</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($segments as $segment)
                        <a href="{{ $segment->localizedUrl($locale) }}" class="group flex flex-col rounded-card border border-line bg-surface p-6 transition hover:border-primary hover:shadow-md">
                            <h3 class="text-lg font-semibold group-hover:text-primary">{{ $t($segment, 'title') }}</h3>
                            @if (filled($t($segment, 'intro')))
                                <p class="mt-2 flex-1 text-muted">{{ $t($segment, 'intro') }}</p>
                            @endif
                            <span class="mt-4 text-sm font-semibold text-primary">{{ __('site.more') }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($packages->isNotEmpty())
        <section class="mx-auto max-w-site px-4 py-16 sm:px-6">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('site.home_packages_title') }}</h2>
            <x-site.package-grid class="mt-10" :packages="$packages" :locale="$locale" :cta-url="$contactUrl" show-service />
        </section>
    @endif

    @if ($page && filled($t($page, 'body')))
        <section class="mx-auto max-w-site px-4 py-16 sm:px-6">
            <div class="prose max-w-3xl">{!! $t($page, 'body') !!}</div>
        </section>
    @endif

    <x-site.cta-band :url="$contactUrl" />
</x-layouts.app>
