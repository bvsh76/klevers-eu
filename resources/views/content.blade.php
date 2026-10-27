@php($t = fn (string $field) => $item->getTranslation($field, $locale, false))
<x-layouts.app :title="$t('meta_title') ?: $t('title')" :description="$t('meta_description') ?: null">
    <article>
        <h1>{{ $t('title') }}</h1>
        @if ($item->isTranslatableAttribute('intro') && filled($t('intro')))
            <p>{{ $t('intro') }}</p>
        @endif
        {!! $t('body') !!}
    </article>
</x-layouts.app>