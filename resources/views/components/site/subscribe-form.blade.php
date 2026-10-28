@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;
    $locale = app()->getLocale();
    $privacyUrl = rescue(fn () => \App\Models\Page::query()->published()->where('key', 'privacy')->first()?->localizedUrl($locale), null, false);
@endphp
<div id="subscribe" class="grid scroll-mt-24 gap-4 lg:grid-cols-2 lg:items-start">
    <div>
        <p class="font-semibold">{{ __('site.subscribe_title') }}</p>
        <p class="mt-1 text-sm text-muted">{{ __('site.subscribe_text') }}</p>
    </div>
    @if (session('subscribe_sent'))
        <p role="status" class="text-sm font-medium text-success">{{ __('site.subscribe_sent') }}</p>
    @else
        <form method="post" action="{{ route('subscribe', ['locale' => $locale]) }}" class="space-y-2">
            @csrf
            <div class="absolute -left-[9999px]" aria-hidden="true">
                <label>Website <input type="text" name="sub_website" tabindex="-1" autocomplete="off"></label>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <label for="sub_email" class="sr-only">{{ __('site.form_email') }}</label>
                <input id="sub_email" type="email" name="sub_email" value="{{ old('sub_email') }}" required maxlength="254" autocomplete="email" placeholder="{{ __('site.subscribe_placeholder') }}" class="min-w-0 flex-1 rounded-md border border-line bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 font-semibold text-white hover:bg-primary-hover">{{ __('site.subscribe_submit') }}</button>
            </div>
            @error('sub_email') <span class="block text-sm text-danger">{{ $message }}</span> @enderror
            <label class="flex items-start gap-2 text-xs text-muted">
                <input type="checkbox" name="sub_consent" value="1" required @checked(old('sub_consent')) class="mt-0.5 size-4 rounded border-line text-primary focus:ring-primary">
                <span>{{ __('site.subscribe_consent') }}
                    @if ($privacyUrl)
                        <a href="{{ $privacyUrl }}" class="underline hover:no-underline">{{ __('site.form_privacy') }}</a>
                    @endif
                </span>
            </label>
            @error('sub_consent') <span class="block text-sm text-danger">{{ $message }}</span> @enderror
        </form>
    @endif
</div>