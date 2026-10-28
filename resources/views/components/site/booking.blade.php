@php
    $embed = \App\Models\Setting::get('booking_embed');
@endphp
@if ($embed)
    <div id="booking" data-consent-block {{ $attributes->class('scroll-mt-24 rounded-card border border-line bg-surface p-6 sm:p-8') }}>
        <h2 class="text-2xl font-bold tracking-tight">{{ __('site.booking_title') }}</h2>
        <p class="mt-2 text-muted">{{ __('site.booking_text') }}</p>
        <div class="mt-6">
            <div data-consent-placeholder class="rounded-card border border-dashed border-line bg-surface-alt p-6 text-center">
                <p class="text-sm text-muted">{{ __('site.booking_consent_text') }}</p>
                <button type="button" x-data x-on:click="$dispatch('consent-grant-external')" class="mt-4 inline-flex rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.booking_consent_button') }}</button>
            </div>
            <template data-consent="external">{!! $embed !!}</template>
        </div>
    </div>
@endif