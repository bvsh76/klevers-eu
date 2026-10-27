@props(['package', 'locale', 'ctaUrl' => null, 'showService' => false])
@php
    $features = $package->getTranslation('features', $locale, false);
    $features = is_array($features) ? array_filter($features) : [];
    $description = $package->getTranslation('description', $locale, false);
@endphp
<div @class([
    'relative flex flex-col rounded-card border bg-surface p-6',
    'border-primary ring-1 ring-primary' => $package->is_featured,
    'border-line' => ! $package->is_featured,
])>
    @if ($package->is_featured)
        <span class="absolute -top-3 left-6 rounded-full bg-accent px-3 py-0.5 text-xs font-semibold text-white">{{ __('site.package_featured') }}</span>
    @endif
    @if ($showService && $package->service)
        <p class="text-xs font-semibold tracking-wide text-muted uppercase">{{ $package->service->getTranslation('title', $locale, false) }}</p>
    @endif
    <h3 class="mt-1 text-lg font-semibold">{{ $package->getTranslation('title', $locale, false) }}</h3>
    <p class="mt-4">
        @if ($package->isOnRequest())
            <span class="text-2xl font-bold">{{ __('site.price_on_request') }}</span>
        @else
            <span class="text-3xl font-bold">{{ \App\Support\Price::format($package->price, $locale) }}</span>
            @if ($package->price_period)
                <span class="text-muted">{{ __('site.period_'.$package->price_period->value) }}</span>
            @endif
        @endif
    </p>
    @if (filled($description))
        <p class="mt-3 text-muted">{{ $description }}</p>
    @endif
    @if ($features)
        <ul class="mt-4 space-y-2 text-sm">
            @foreach ($features as $feature)
                <li class="flex gap-2">
                    <svg class="mt-0.5 size-4 shrink-0 text-success" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    @endif
    <div class="flex-1"></div>
    @if ($ctaUrl)
        <a href="{{ $ctaUrl }}" @class([
            'mt-6 inline-flex justify-center rounded-md px-4 py-2 text-sm font-semibold',
            'bg-primary text-white hover:bg-primary-hover' => $package->is_featured,
            'border border-primary text-primary hover:bg-primary-soft' => ! $package->is_featured,
        ])>{{ __('site.cta_consultation') }}</a>
    @endif
</div>
