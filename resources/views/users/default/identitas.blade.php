
<div class="mb-3">
    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
    <input class="form-control @error('tempat_lahir') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->{$role}->tempat_lahir : old('tempat_lahir') }}" id="tempat_lahir"
        placeholder="Tempat Lahir" name="tempat_lahir" />
    @error('tempat_lahir')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
    <input class="form-control @error('tgl_lahir') is-invalid @enderror" type="date"
        value="{{ isset($data) ? $data->{$role}->tgl_lahir : old('tgl_lahir') }}" id="tgl_lahir"
        placeholder="Tempat Lahir" name="tgl_lahir" />
    @error('tgl_lahir')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="agama_id" class="form-label">Agama</label>
    <select class="form-select @error('agama_id') is-invalid @enderror" name="agama_id">
        <option value="">Pilih Agama</option>
        @foreach ($agamas as $agama)
            <option value="{{ $agama->id }}"
                {{ isset($data) ? ($data->{$role}->agama_id == $agama->id ? 'selected' : '') : (old('agama_id') == $agama->id ? 'selected' : '') }}>
                {{ $agama->nama }}</option>
        @endforeach
    </select>
    @error('agama_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="status" id="publish"
            {{ isset($data) ? ($data->{$role}->status ? 'checked' : '') : (old('status') ? 'checked' : '') }} {{ $page == 'profile' ? 'disabled' : '' }}>
    </div>
    @error('jk')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="jk" class="form-label">Jenis Kelamin</label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="jk" value="l" id="jk-l"
                {{ isset($data) ? ($data->{$role}->jk == 'l' ? 'checked' : '') : (old('jk') == 'l' ? 'checked' : '') }}>
            <label class="form-check-label" for="jk-l">
                Laki-laki
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="jk" value="p" id="jk-p"
                {{ isset($data) ? ($data->{$role}->jk == 'p' ? 'checked' : '') : (old('jk') == 'p' ? 'checked' : '') }}>
            <label class="form-check-label" for="jk-p">
                Perempuan
            </label>
        </div>
    </div>
    @error('jk')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
