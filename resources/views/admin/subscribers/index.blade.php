<x-admin.layout title="Подписчики">
    <div class="head">
        <h1>Подписчики</h1>
        <a class="button" href="{{ route('admin.subscribers.export', request()->query()) }}">Экспорт CSV</a>
    </div>
    <form method="get" class="filters">
        <select name="state">
            <option value="">Все</option>
            <option value="active" @selected(request('state') === 'active')>Активные</option>
            <option value="unsubscribed" @selected(request('state') === 'unsubscribed')>Отписались</option>
        </select>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="E-mail">
        <button type="submit">Найти</button>
    </form>
    <table>
        <thead>
            <tr><th>ID</th><th>E-mail</th><th>Язык</th><th>Источник</th><th>Подписан</th><th>Подтверждён</th><th>Отписан</th></tr>
        </thead>
        <tbody>
            @forelse ($items as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ strtoupper($s->locale) }}</td>
                    <td>{{ $s->source }}</td>
                    <td>{{ \App\Support\Csv::date($s->created_at) }}</td>
                    <td>{{ \App\Support\Csv::date($s->confirmed_at) }}</td>
                    <td>{{ \App\Support\Csv::date($s->unsubscribed_at) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Подписчиков нет.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links() }}
</x-admin.layout>