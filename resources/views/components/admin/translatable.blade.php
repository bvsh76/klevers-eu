@props(['name', 'label', 'type' => 'input', 'model' => null, 'locales', 'default', 'required' => false])
<fieldset class="tr">
    <legend>{{ $label }}</legend>
    @foreach ($locales as $locale)
        @php($value = old("{$name}.{$locale}", $model?->exists ? $model->getTranslation($name, $locale, false) : ''))
        @php($value = is_array($value) ? implode("\n", $value) : $value)
        <label>
            <span class="lang">{{ strtoupper($locale) }}@if ($required && $locale === $default) *@endif</span>
            @if ($type === 'html')
                <textarea name="{{ $name }}[{{ $locale }}]" data-editor>{{ $value }}</textarea>
                @once
                    @push('scripts')
                        @vite('resources/js/admin-editor.js')
                    @endpush
                @endonce
            @elseif (in_array($type, ['textarea', 'lines'], true))
                <textarea name="{{ $name }}[{{ $locale }}]">{{ $value }}</textarea>
            @else
                <input type="text" name="{{ $name }}[{{ $locale }}]" value="{{ $value }}">
            @endif
            @error("{$name}.{$locale}")
                <span class="error">{{ $message }}</span>
            @enderror
        </label>
    @endforeach
</fieldset>