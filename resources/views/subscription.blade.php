<x-layouts.app :title="$title" :alternates="false">
    <section class="mx-auto max-w-site px-4 py-16 sm:px-6">
        <div class="max-w-2xl rounded-card border border-line bg-surface p-8">
            <h1 class="text-3xl font-bold tracking-tight text-primary">{{ $title }}</h1>
            <p class="mt-4 text-lg text-muted">{{ $text }}</p>

            @if ($state === 'confirmed' && $magnetUrl)
                <a href="{{ $magnetUrl }}" target="_blank" rel="noopener" class="mt-6 inline-flex rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.lead_magnet_download') }}</a>
            @endif

            @if ($state === 'unsubscribe')
                <form method="post" action="{{ route('unsubscribe.store', ['locale' => app()->getLocale(), 'token' => $token]) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="inline-flex rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.unsubscribe_button') }}</button>
                </form>
            @endif

            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="mt-6 inline-block text-primary underline hover:no-underline">{{ __('site.back_to_home') }}</a>
        </div>
    </section>
</x-layouts.app>