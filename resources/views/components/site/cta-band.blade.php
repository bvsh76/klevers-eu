@props(['url' => null])
@if ($url)
    <section class="mx-auto mt-16 max-w-site px-4 sm:px-6">
        <div class="rounded-card bg-primary px-6 py-12 text-center text-white sm:px-12">
            <h2 class="text-3xl font-bold tracking-tight">{{ __('site.cta_title') }}</h2>
            <p class="mt-3 text-white/80">{{ __('site.cta_text') }}</p>
            <a href="{{ $url }}" class="mt-6 inline-flex rounded-md bg-white px-5 py-3 font-semibold text-primary hover:bg-primary-soft">{{ __('site.cta_consultation') }}</a>
        </div>
    </section>
@endif
