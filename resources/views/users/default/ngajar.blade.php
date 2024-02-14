<div class="mb-3">
    <label for="mampu_handle_kebutuhan_khusus" class="form-label">Mampu handle
        Kebutuhan
        Khusus?</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_khusus" value="1"
                id="mampu_handle_kebutuhan_khusus-1"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_khusus == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_khusus') == 1 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_khusus-1">
                Ya
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_khusus" value="0"
                id="mampu_handle_kebutuhan_khusus-0"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_khusus == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_khusus') == 0 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_khusus-0">
                Tidak
            </label>
        </div>
    </div>
    @error('mampu_handle_kebutuhan_khusus')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="mampu_handle_kebutuhan_braille" class="form-label">Mampu
        handleKebutuhan
        braille?</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_braille" value="1"
                id="mampu_handle_kebutuhan_braille-1"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_braille == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_braille') == 1 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_braille-1">
                Ya
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_braille" value="0"
                id="mampu_handle_kebutuhan_braille-0"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_braille == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_braille') == 0 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_braille-0">
                Tidak
            </label>
        </div>
    </div>
    @error('mampu_handle_kebutuhan_braille')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="mampu_handle_kebutuhan_bahasa_isyarat" class="form-label">Mampu handle
        Kebutuhan
        bahasa isyarat?</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_bahasa_isyarat" value="1"
                id="mampu_handle_kebutuhan_bahasa_isyarat-1"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_bahasa_isyarat == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_bahasa_isyarat') == 1 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_bahasa_isyarat-1">
                Ya
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="mampu_handle_kebutuhan_bahasa_isyarat" value="0"
                id="mampu_handle_kebutuhan_bahasa_isyarat-0"
                {{ isset($data) ? ($data->{request('role')}->mampu_handle_kebutuhan_bahasa_isyarat == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_bahasa_isyarat') == 0 ? 'checked' : '') }}>
            <label class="form-check-label" for="mampu_handle_kebutuhan_bahasa_isyarat-0">
                Tidak
            </label>
        </div>
    </div>
    @error('mampu_handle_kebutuhan_bahasa_isyarat')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
