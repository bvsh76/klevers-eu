<nav aria-label="{{ __('site.language') }}">
    <ul>
        @foreach (config('locales.supported') as $code => $name)
            <li>
                @if ($code === app()->getLocale())
                    <span aria-current="true" lang="{{ $code }}">{{ $name }}</span>
                @else
                    <a href="{{ \App\Support\LocaleUrl::to($code) }}" hreflang="{{ $code }}" lang="{{ $code }}">{{ $name }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>