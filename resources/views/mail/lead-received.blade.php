<x-mail::message>
# Новая заявка №{{ $lead->id }}

**Имя:** {{ $lead->name }}

**E-mail:** {{ $lead->email }}

@if (filled($lead->phone))
**Телефон:** {{ $lead->phone }}

@endif
**Язык:** {{ strtoupper($lead->locale) }} · **Страница:** {{ $lead->source }}

**Дата:** {{ $lead->created_at?->timezone(config('app.display_timezone'))->format('d.m.Y H:i') }}

@if (filled($lead->message))
**Сообщение:**

{{ $lead->message }}

@endif
<x-mail::button :url="route('admin.leads.show', $lead->id)">
Открыть в админке
</x-mail::button>

Ответить клиенту можно прямо на это письмо.
</x-mail::message>