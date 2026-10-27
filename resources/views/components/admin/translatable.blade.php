@props(['name', 'label', 'type' => 'input', 'model' => null, 'locales', 'default', 'required' => false])
<fieldset class="tr">
    <legend>{{ $label }}</legend>
    @foreach ($locales as $locale)
        @php($value = old("{$name}.{$locale}", $model?->exists ? $model->getTranslation($name, $locale, false) : ''))
        <label>
            <span class="lang">{{ strtoupper($locale) }}@if ($required && $locale === $default) *@endif</span>
            @if ($type === 'textarea')
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