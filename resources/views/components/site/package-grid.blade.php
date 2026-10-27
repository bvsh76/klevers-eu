@props(['packages', 'locale', 'ctaUrl' => null, 'showService' => false])
<div {{ $attributes->class([
    'grid gap-6',
    'max-w-md' => $packages->count() === 1,
    'max-w-3xl md:grid-cols-2' => $packages->count() === 2,
    'md:grid-cols-3' => $packages->count() >= 3,
]) }}>
    @foreach ($packages as $package)
        <x-site.package-card :package="$package" :locale="$locale" :cta-url="$ctaUrl" :show-service="$showService" />
    @endforeach
</div>
