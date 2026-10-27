<x-admin.layout :title="$item->exists ? $labels['edit'] : $labels['create']">
    <div class="head">
        <h1>{{ $item->exists ? $labels['edit'] : $labels['create'] }}</h1>
        <a href="{{ route($prefix.'.index') }}">← {{ $labels['plural'] }}</a>
    </div>

    @error('delete')
        <div class="alert error-box">{{ $message }}</div>
    @enderror

    <form method="post" action="{{ $item->exists ? route($prefix.'.update', $item->id) : route($prefix.'.store') }}" class="card form">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        @foreach ($fields as $name => [$label, $type, $required])
            <x-admin.translatable :name="$name" :label="$label" :type="$type" :required="$required" :model="$item" :locales="$locales" :default="$default" />
        @endforeach

        @if ($extraView)
            @include($extraView)
        @endif

        <div class="row">
            <label>Ключ (системный, для кода; можно пусто)
                <input type="text" name="key" value="{{ old('key', $item->key) }}">
                @error('key') <span class="error">{{ $message }}</span> @enderror
            </label>
            <label>Порядок
                <input type="number" name="sort" min="0" value="{{ old('sort', $item->sort ?? 0) }}">
                @error('sort') <span class="error">{{ $message }}</span> @enderror
            </label>
            <label class="check">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))>
                Опубликовано
            </label>
        </div>

        <div class="actions">
            <button type="submit">Сохранить</button>
        </div>
    </form>

    @if ($item->exists && blank($item->key))
        <form method="post" action="{{ route($prefix.'.destroy', $item->id) }}" class="actions" onsubmit="return confirm('Удалить безвозвратно?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger">Удалить</button>
        </form>
    @endif
</x-admin.layout>