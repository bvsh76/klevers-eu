@php
    $t = fn ($model, string $field) => $model->getTranslation($field, $locale, false);
    $intro = $item->isTranslatableAttribute('intro') ? $t($item, 'intro') : null;
@endphp
<x-layouts.app :title="$t($item, 'meta_title') ?: $t($item, 'title')" :description="$t($item, 'meta_description') ?: null">
    <section class="bg-surface-alt">
        <div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:py-16">
            <h1 class="max-w-3xl text-3xl font-bold tracking-tight text-primary sm:text-4xl">{{ $t($item, 'title') }}</h1>
            @if (filled($intro))
                <p class="mt-4 max-w-2xl text-lg text-muted">{{ $intro }}</p>
            @endif
        </div>
    </section>

    @if (filled($t($item, 'body')))
        <article class="mx-auto max-w-site px-4 pt-12 sm:px-6">
            <div class="prose max-w-3xl">{!! \App\Support\Html::clean($t($item, 'body')) !!}</div>
        </article>
    @endif

    @if ($packages->isNotEmpty())
        <section class="mx-auto max-w-site px-4 pt-16 sm:px-6">
            <h2 class="text-2xl font-bold tracking-tight">{{ __('site.service_packages') }}</h2>
            <x-site.package-grid class="mt-10" :packages="$packages" :locale="$locale" :cta-url="$contactUrl" />
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-site px-4 pt-16 sm:px-6">
            <h2 class="text-2xl font-bold tracking-tight">{{ $item instanceof \App\Models\Service ? __('site.related_segments') : __('site.related_services') }}</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $rel)
                    <a href="{{ $rel->localizedUrl($locale) }}" class="group flex flex-col rounded-card border border-line bg-surface p-6 transition hover:border-primary hover:shadow-md">
                        <h3 class="text-lg font-semibold group-hover:text-primary">{{ $t($rel, 'title') }}</h3>
                        @if (filled($t($rel, 'intro')))
                            <p class="mt-2 flex-1 text-muted">{{ $t($rel, 'intro') }}</p>
                        @endif
                        <span class="mt-4 text-sm font-semibold text-primary">{{ __('site.more') }} &rarr;</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <x-site.cta-band :url="$contactUrl" />
</x-layouts.app>
