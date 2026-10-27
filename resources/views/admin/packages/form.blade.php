<x-admin.layout :title="$item->exists ? 'Редактирование пакета' : 'Новый пакет'">
    <div class="head">
        <h1>{{ $item->exists ? 'Редактирование пакета' : 'Новый пакет' }}</h1>
        <a href="{{ route('admin.packages.index') }}">← Пакеты</a>
    </div>

    @error('delete')
        <div class="alert error-box">{{ $message }}</div>
    @enderror

    <form method="post" action="{{ $item->exists ? route('admin.packages.update', $item->id) : route('admin.packages.store') }}" class="card form">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        @foreach ($fields as $name => [$label, $type, $required])
            <x-admin.translatable :name="$name" :label="$label" :type="$type" :required="$required" :model="$item" :locales="$locales" :default="$default" />
        @endforeach

        <div class="row">
            <label>Услуга
                <select name="service_id">
                    <option value="">— без услуги —</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected((string) old('service_id', $item->service_id) === (string) $service->id)>{{ $service->getTranslation('title', $default, false) }}</option>
                    @endforeach
                </select>
                @error('service_id') <span class="error">{{ $message }}</span> @enderror
            </label>
            <label>Цена, € (пусто — «по запросу»)
                <input type="text" name="price" inputmode="decimal" value="{{ old('price', $item->price) }}">
                @error('price') <span class="error">{{ $message }}</span> @enderror
            </label>
            <label>Период
                <select name="price_period">
                    <option value="">—</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->value }}" @selected(old('price_period', $item->price_period?->value) === $period->value)>{{ $period->label() }}</option>
                    @endforeach
                </select>
                @error('price_period') <span class="error">{{ $message }}</span> @enderror
            </label>
        </div>

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
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
                Активен (показывать на сайте)
            </label>
            <label class="check">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))>
                Выделить как рекомендуемый
            </label>
        </div>

        <div class="actions">
            <button type="submit">Сохранить</button>
        </div>
    </form>

    @if ($item->exists && blank($item->key))
        <form method="post" action="{{ route('admin.packages.destroy', $item->id) }}" class="actions" onsubmit="return confirm('Удалить безвозвратно?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger">Удалить</button>
        </form>
    @endif
</x-admin.layout>