<x-admin.layout title="Заявки">
    <div class="head">
        <h1>Заявки</h1>
        <a class="button" href="{{ route('admin.leads.export', request()->query()) }}">Экспорт CSV</a>
    </div>
    <form method="get" class="filters">
        <select name="status">
            <option value="">Все статусы</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Имя, e-mail или телефон">
        <button type="submit">Найти</button>
    </form>
    <table>
        <thead>
            <tr><th>ID</th><th>Дата</th><th>Статус</th><th>Имя</th><th>E-mail</th><th>Телефон</th><th>Язык</th><th>Услуга</th></tr>
        </thead>
        <tbody>
            @forelse ($items as $lead)
                <tr>
                    <td><a href="{{ route('admin.leads.show', $lead->id) }}">{{ $lead->id }}</a></td>
                    <td>{{ \App\Support\Csv::date($lead->created_at) }}</td>
                    <td><span class="badge {{ $lead->status->value }}">{{ $lead->status->label() }}</span></td>
                    <td><a href="{{ route('admin.leads.show', $lead->id) }}">{{ $lead->name }}</a></td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ strtoupper($lead->locale) }}</td>
                    <td>{{ $lead->service?->getTranslation('title', config('locales.default'), false) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Заявок нет.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links() }}
</x-admin.layout>