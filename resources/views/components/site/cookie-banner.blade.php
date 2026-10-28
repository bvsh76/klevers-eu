@php
    $privacyUrl = rescue(fn () => \App\Models\Page::query()->published()->where('key', 'privacy')->first()?->localizedUrl(app()->getLocale()), null, false);
@endphp
<div x-data="consent" x-show="open" x-cloak class="fixed inset-x-0 bottom-0 z-50 p-4" role="dialog" aria-labelledby="cookie-title">
    <div class="mx-auto max-w-3xl rounded-card border border-line bg-surface p-6 shadow-xl">
        <p id="cookie-title" class="text-lg font-semibold">{{ __('site.cookie_title') }}</p>
        <p class="mt-2 text-sm text-muted">{{ __('site.cookie_text') }}
            @if ($privacyUrl)
                <a href="{{ $privacyUrl }}" class="text-primary underline hover:no-underline">{{ __('site.form_privacy') }}</a>
            @endif
        </p>

        <div x-show="settings" class="mt-4 space-y-3">
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" checked disabled class="mt-0.5 size-4 rounded border-line">
                <span><span class="font-medium">{{ __('site.cookie_cat_necessary') }}</span><span class="block text-muted">{{ __('site.cookie_cat_necessary_text') }}</span></span>
            </label>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" x-model="analytics" class="mt-0.5 size-4 rounded border-line text-primary focus:ring-primary">
                <span><span class="font-medium">{{ __('site.cookie_cat_analytics') }}</span><span class="block text-muted">{{ __('site.cookie_cat_analytics_text') }}</span></span>
            </label>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" x-model="external" class="mt-0.5 size-4 rounded border-line text-primary focus:ring-primary">
                <span><span class="font-medium">{{ __('site.cookie_cat_external') }}</span><span class="block text-muted">{{ __('site.cookie_cat_external_text') }}</span></span>
            </label>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <button type="button" x-on:click="acceptAll()" class="rounded-md bg-primary px-4 py-2 font-semibold text-white hover:bg-primary-hover">{{ __('site.cookie_accept_all') }}</button>
            <button type="button" x-on:click="onlyNecessary()" class="rounded-md bg-primary px-4 py-2 font-semibold text-white hover:bg-primary-hover">{{ __('site.cookie_necessary_only') }}</button>
            <button type="button" x-show="!settings" x-on:click="settings = true" class="rounded-md border border-line px-4 py-2 font-semibold hover:bg-surface-alt">{{ __('site.cookie_settings') }}</button>
            <button type="button" x-show="settings" x-on:click="saveSettings()" class="rounded-md border border-line px-4 py-2 font-semibold hover:bg-surface-alt">{{ __('site.cookie_save') }}</button>
        </div>
    </div>
</div>