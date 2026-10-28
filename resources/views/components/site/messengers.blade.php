@props(['variant' => 'icons'])
@php
    $wa = \App\Models\Setting::get('whatsapp');
    $tg = \App\Models\Setting::get('telegram');
    $items = collect([
        $wa ? ['url' => 'https://wa.me/'.$wa, 'name' => 'WhatsApp', 'icon' => 'chat'] : null,
        $tg ? ['url' => 'https://t.me/'.$tg, 'name' => 'Telegram', 'icon' => 'plane'] : null,
    ])->filter();
    $paths = [
        'chat' => 'M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z',
        'plane' => 'M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5',
    ];
@endphp
@if ($items->isNotEmpty())
    <div {{ $attributes->class('flex items-center gap-2') }}>
        @foreach ($items as $item)
            @if ($variant === 'buttons')
                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-md border border-line px-4 py-2 font-semibold hover:border-primary hover:text-primary">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $paths[$item['icon']] }}"/></svg>
                    {{ __('site.messenger_write', ['name' => $item['name']]) }}
                </a>
            @else
                <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="rounded-md p-2 text-muted hover:bg-surface-alt hover:text-primary" aria-label="{{ __('site.messenger_write', ['name' => $item['name']]) }}" title="{{ $item['name'] }}">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $paths[$item['icon']] }}"/></svg>
                </a>
            @endif
        @endforeach
    </div>
@endif