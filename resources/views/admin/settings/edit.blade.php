<x-admin.layout title="Настройки">
    <div class="head">
        <h1>Настройки сайта</h1>
    </div>

    <form method="post" action="{{ route('admin.settings.update') }}" class="card form">
        @csrf
        @method('PUT')

        @foreach ($fields as $key => [$label, $type])
            @php
                $stored = $values[$key] ?? null;
                if ($key === 'whatsapp' && filled($stored)) {
                    $stored = '+'.$stored;
                }
            @endphp
            <label>{{ $label }}
                @if ($type === 'textarea')
                    <textarea name="{{ $key }}" rows="6" spellcheck="false">{{ old($key, $stored) }}</textarea>
                @else
                    <input type="text" name="{{ $key }}" value="{{ old($key, $stored) }}">
                @endif
                @error($key) <span class="error">{{ $message }}</span> @enderror
            </label>
        @endforeach

        <div class="actions">
            <button type="submit">Сохранить</button>
        </div>
    </form>
</x-admin.layout>