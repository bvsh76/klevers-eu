<fieldset class="tr">
    <legend>Сегменты, для которых услуга</legend>
    @forelse ($allSegments as $segment)
        <label class="check">
            <input type="checkbox" name="segments[]" value="{{ $segment->id }}" @checked(in_array($segment->id, $selectedSegments, true))>
            {{ $segment->getTranslation('title', $default, false) }}
        </label>
    @empty
        <span class="muted">Сегментов пока нет.</span>
    @endforelse
    @error('segments.*') <span class="error">{{ $message }}</span> @enderror
</fieldset>