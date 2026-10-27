@props(['code', 'title', 'text'])
<x-layouts.app :title="$title" :alternates="false">
    <section class="mx-auto max-w-site px-4 py-24 sm:px-6">
        <p class="text-sm font-semibold tracking-wide text-accent">{{ $code }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-primary sm:text-4xl">{{ $title }}</h1>
        <p class="mt-4 max-w-xl text-lg text-muted">{{ $text }}</p>
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="mt-8 inline-flex rounded-md bg-primary px-5 py-3 font-semibold text-white hover:bg-primary-hover">{{ __('site.back_home') }}</a>
    </section>
</x-layouts.app>
