@php
    $disabled = isset($disabled) ? $disabled : false;
@endphp

<div class="mb-3">
    <label for="nama_{{ $value }}" class="form-label">Nama {{ $value }}</label>
    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error("nama_{$value}") is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->mahasiswa->{'nama_' . $value} : old("nama_{$value}") }}"
        id="nama_{{ $value }}" placeholder="Nama {{ $value }}" name="nama_{{ $value }}" />
    @error('nama_{{ $value }}')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="tgl_lahir_{{ $value }}" class="form-label">Tanggal Lahir {{ $value }}</label>
    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error("tgl_lahir_{$value}") is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->mahasiswa->{'tgl_lahir_' . $value} : old("tgl_lahir_{$value}") }}"
        id="tgl_lahir_{{ $value }}" placeholder="Tanggal Lahir {{ $value }}"
        name="tgl_lahir_{{ $value }}" />
    @error('tgl_lahir_{{ $value }}')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="nik_{{ $value }}" class="form-label">NIK {{ $value }}</label>
    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error("nik_{$value}") is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->mahasiswa->{'nik_' . $value} : old("nik_{$value}") }}"
        id="nik_{{ $value }}" placeholder="NIK {{ $value }}" name="nik_{{ $value }}" />
    @error('nik_{{ $value }}')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="jenjang_{{ $value }}_id" class="form-label">Jenjang {{ $value }}</label>
    <select {{ $disabled ? 'disabled' : '' }} class="form-select @error("jenjang_{$value}_id") is-invalid @enderror"
        name="jenjang_{{ $value }}_id">
        <option value="">Pilih Jenjang</option>
        @foreach ($jenjang as $item)
            <option value="{{ $item->id }}"
                {{ isset($data) ? ($data->mahasiswa->{'jenjang_' . $value . '_id'} == $item->id ? 'selected' : '') : (old("jenjang_{$value}_id") == $item->id ? 'selected' : '') }}>
                {{ $item->nama }}</option>
        @endforeach
    </select>
    @error('jenjang_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="pekerjaan_{{ $value }}_id" class="form-label">Pekerjaan {{ $value }}</label>
    <select {{ $disabled ? 'disabled' : '' }} class="form-select @error("pekerjaan_{$value}_id") is-invalid @enderror"
        name="pekerjaan_{{ $value }}_id">
        <option value="">Pilih Pekerjaan</option>
        @foreach ($pekerjaans as $pekerjaan)
            <option value="{{ $pekerjaan->id }}"
                {{ isset($data) ? ($data->mahasiswa->{'pekerjaan_' . $value . '_id'} == $pekerjaan->id ? 'selected' : '') : (old("pekerjaan_{$value}_id") == $pekerjaan->id ? 'selected' : '') }}>
                {{ $pekerjaan->nama }}</option>
        @endforeach
    </select>
    @error('pekerjaan_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="penghasilan_{{ $value }}_id" class="form-label">Penghasilan {{ $value }}</label>
    <select {{ $disabled ? 'disabled' : '' }} class="form-select @error("penghasilan_{$value}_id") is-invalid @enderror"
        name="penghasilan_{{ $value }}_id">
        <option value="">Pilih Penghasilan</option>
        @foreach ($penghasilans as $penghasilan)
            <option value="{{ $penghasilan->id }}"
                {{ isset($data) ? ($data->mahasiswa->{'penghasilan_' . $value . '_id'} == $penghasilan->id ? 'selected' : '') : (old("penghasilan_{$value}_id") == $penghasilan->id ? 'selected' : '') }}>
                {{ $penghasilan->nama }}</option>
        @endforeach
    </select>
    @error('penghasilan_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
@if ($value == 'ayah' || $value == 'ibu')
    <div class="mb-3">
        <label for="kebutuhan_khusus" class="form-label">Kebutuhan Khusus?</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio" name="{{ $value }}_kebutuhan_khusus" value="1" id="kps_1">
                <label class="form-check-label" for="kps_1">
                    Ya
                </label>
            </div>
            <div class="form-check">
                <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio" name="{{ $value }}_kebutuhan_khusus" value="0" id="kps_0"
                    checked>
                <label class="form-check-label" for="kps_0">
                    Tidak
                </label>
            </div>
        </div>
        @error($value . '_kebutuhan_khusus')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
    </div>
@endif
