<x-layouts.app :title="__('site.error_500_title')" :alternates="false">
    <h1>{{ __('site.error_500_title') }}</h1>
    <p>{{ __('site.error_500_text') }}</p>
    <p><a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.back_home') }}</a></p>
</x-layouts.app>
