<div class="mb-3">
    <label for="nama_{{ $value }}" class="form-label">Nama {{ $value }}</label>
    <input class="form-control @error("nama_{$value}") is-invalid @enderror" type="text"
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
    <input class="form-control @error("tgl_lahir_{$value}") is-invalid @enderror" type="text"
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
    <input class="form-control @error("nik_{$value}") is-invalid @enderror" type="text"
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
    <select class="form-select @error("jenjang_{$value}_id") is-invalid @enderror" name="jenjang_{{ $value }}_id">
        <option value="">Pilih Jenjang</option>
    </select>
    @error('jenjang_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="pekerjaan_{{ $value }}_id" class="form-label">Pekerjaan {{ $value }}</label>
    <select class="form-select @error("pekerjaan_{$value}_id") is-invalid @enderror" name="pekerjaan_{{ $value }}_id">
        <option value="">Pilih Pekerjaan</option>
    </select>
    @error('pekerjaan_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="penghasilan_{{ $value }}_id" class="form-label">Penghasilan {{ $value }}</label>
    <select class="form-select @error("penghasilan_{$value}_id") is-invalid @enderror" name="penghasilan_{{ $value }}_id">
        <option value="">Pilih Penghasilan</option>
    </select>
    @error('penghasilan_{{ $value }}_id')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
