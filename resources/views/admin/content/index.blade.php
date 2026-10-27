<x-admin.layout :title="$labels['plural']">
    <div class="head">
        <h1>{{ $labels['plural'] }}</h1>
        <a class="button" href="{{ route($prefix.'.create') }}">+ Добавить</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Слаги</th>
                <th>Ключ</th>
                <th>Опубликовано</th>
                <th>Порядок</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td><a href="{{ route($prefix.'.edit', $item->id) }}">{{ $item->getTranslation('title', $default, false) ?: '—' }}</a></td>
                    <td class="muted">
                        @foreach ($locales as $locale)
                            <div>{{ strtoupper($locale) }}: {{ $item->getTranslation('slug', $locale, false) ?: '—' }}</div>
                        @endforeach
                    </td>
                    <td>{{ $item->key ?? '' }}</td>
                    <td>{{ $item->is_published ? 'да' : 'нет' }}</td>
                    <td>{{ $item->sort }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Пока пусто.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links() }}
</x-admin.layout>