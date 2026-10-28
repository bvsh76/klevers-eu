@props(['locale'])
@php
    $privacyUrl = rescue(fn () => \App\Models\Page::query()->published()->where('key', 'privacy')->first()?->localizedUrl($locale), null, false);
    $input = 'mt-1 block w-full rounded-md border border-line bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp
<div id="lead-form" {{ $attributes->class('scroll-mt-24 rounded-card border border-line bg-surface p-6 sm:p-8') }}>
    @if (session('lead_sent'))
        <div role="status">
            <h2 class="text-2xl font-bold tracking-tight">{{ __('site.lead_sent_title') }}</h2>
            <p class="mt-2 text-muted">{{ __('site.lead_sent_text') }}</p>
        </div>
    @else
        <h2 class="text-2xl font-bold tracking-tight">{{ __('site.lead_form_title') }}</h2>
        <p class="mt-2 text-muted">{{ __('site.lead_form_text') }}</p>

        <form method="post" action="{{ route('lead.store', ['locale' => $locale]) }}" class="mt-6 grid gap-4 sm:grid-cols-2">
            @csrf
            <div class="absolute -left-[9999px]" aria-hidden="true">
                <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <label class="block text-sm font-medium">{{ __('site.form_name') }}
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="150" autocomplete="name" class="{{ $input }}">
                @error('name') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-medium">{{ __('site.form_email') }}
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="email" class="{{ $input }}">
                @error('email') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-medium sm:col-span-2">{{ __('site.form_phone') }} <span class="font-normal text-muted">({{ __('site.form_optional') }})</span>
                <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="50" autocomplete="tel" class="{{ $input }}">
                @error('phone') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-medium sm:col-span-2">{{ __('site.form_message') }} <span class="font-normal text-muted">({{ __('site.form_optional') }})</span>
                <textarea name="message" rows="4" maxlength="5000" class="{{ $input }}">{{ old('message') }}</textarea>
                @error('message') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </label>
            <div class="sm:col-span-2">
                <label class="flex items-start gap-3 text-sm">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="mt-0.5 size-4 rounded border-line text-primary focus:ring-primary">
                    <span>{{ __('site.form_consent') }}
                        @if ($privacyUrl)
                            <a href="{{ $privacyUrl }}" class="text-primary underline hover:no-underline">{{ __('site.form_privacy') }}</a>
                        @endif
                    </span>
                </label>
                @error('consent') <span class="mt-1 block text-sm text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.form_submit') }}</button>
            </div>
        </form>
    @endif
</div>