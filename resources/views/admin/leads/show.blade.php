<x-admin.layout :title="'Заявка #'.$lead->id">
    <div class="head">
        <h1>Заявка #{{ $lead->id }}</h1>
        <a href="{{ route('admin.leads.index') }}">← Заявки</a>
    </div>
    <div class="card">
        <table>
            <tr><th>Дата</th><td>{{ \App\Support\Csv::date($lead->created_at) }}</td></tr>
            <tr><th>Имя</th><td>{{ $lead->name }}</td></tr>
            <tr><th>E-mail</th><td><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td></tr>
            <tr><th>Телефон</th><td>{{ $lead->phone }}</td></tr>
            <tr><th>Язык</th><td>{{ strtoupper($lead->locale) }}</td></tr>
            <tr><th>Услуга</th><td>{{ $lead->service?->getTranslation('title', config('locales.default'), false) }}</td></tr>
            <tr><th>Пакет</th><td>{{ $lead->package?->getTranslation('title', config('locales.default'), false) }}</td></tr>
            <tr><th>Источник</th><td>{{ $lead->source }}</td></tr>
            <tr><th>Сообщение</th><td class="pre">{{ $lead->message }}</td></tr>
            <tr><th>Согласие</th><td>{{ \App\Support\Csv::date($lead->consent_at) }}</td></tr>
            <tr><th>UTM</th><td>{{ $lead->utm ? json_encode($lead->utm, JSON_UNESCAPED_UNICODE) : '' }}</td></tr>
        </table>
        <form method="post" action="{{ route('admin.leads.update', $lead->id) }}" class="filters">
            @csrf
            @method('PATCH')
            <select name="status">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($lead->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button type="submit">Сменить статус</button>
        </form>
    </div>
</x-admin.layout>