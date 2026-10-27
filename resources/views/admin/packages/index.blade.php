<x-admin.layout title="Пакеты">
    <div class="head">
        <h1>Пакеты</h1>
        <a class="button" href="{{ route('admin.packages.create') }}">+ Добавить</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Услуга</th>
                <th>Цена</th>
                <th>Активен</th>
                <th>Выделен</th>
                <th>Порядок</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td><a href="{{ route('admin.packages.edit', $item->id) }}">{{ $item->getTranslation('title', $default, false) ?: '—' }}</a></td>
                    <td>{{ $item->service?->getTranslation('title', $default, false) ?? '—' }}</td>
                    <td>{{ $item->isOnRequest() ? 'по запросу' : $item->price.' € '.$item->price_period?->label() }}</td>
                    <td>{{ $item->is_active ? 'да' : 'нет' }}</td>
                    <td>{{ $item->is_featured ? 'да' : '' }}</td>
                    <td>{{ $item->sort }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Пока пусто.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links() }}
</x-admin.layout>