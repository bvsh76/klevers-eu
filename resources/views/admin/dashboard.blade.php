<x-admin.layout title="Дашборд">
    <h1>Дашборд</h1>
    <div class="stats">
        @foreach ($stats as $label => $value)
            <div class="card stat">
                <span>{{ $label }}</span>
                <strong>{{ $value }}</strong>
            </div>
        @endforeach
    </div>
</x-admin.layout>