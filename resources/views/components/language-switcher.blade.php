<nav {{ $attributes }} aria-label="{{ __('site.language') }}">
    <ul class="flex items-center gap-1 text-sm">
        @foreach (config('locales.supported') as $code => $name)
            <li>
                @if ($code === app()->getLocale())
                    <span aria-current="true" lang="{{ $code }}" title="{{ $name }}" class="block rounded-md bg-primary-soft px-2 py-1 font-semibold text-primary"><span aria-hidden="true">{{ strtoupper($code) }}</span><span class="sr-only">{{ $name }}</span></span>
                @else
                    <a href="{{ \App\Support\LocaleUrl::to($code) }}" hreflang="{{ $code }}" lang="{{ $code }}" title="{{ $name }}" class="block rounded-md px-2 py-1 text-muted hover:bg-surface-alt hover:text-ink"><span aria-hidden="true">{{ strtoupper($code) }}</span><span class="sr-only">{{ $name }}</span></a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>