<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="rt" class="form-label">RT</label>
            <input class="form-control @error('rt') is-invalid @enderror" type="number"
                value="{{ isset($data) ? $data->{$role}->rt : old('rt') }}" id="rt" placeholder="rt" name="rt" />
            @error('rt')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="rw" class="form-label">RW</label>
            <input class="form-control @error('rw') is-invalid @enderror" type="number"
                value="{{ isset($data) ? $data->{$role}->rw : old('rw') }}" id="rw" placeholder="rw"
                name="rw" />
            @error('rw')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="jalan" class="form-label">Jalan</label>
    <input class="form-control @error('jalan') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->{$role}->jalan : old('jalan') }}" id="jalan" placeholder="Jalan"
        name="jalan" />
    @error('jalan')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="dusun" class="form-label">Dusun</label>
            <input class="form-control @error('dusun') is-invalid @enderror" type="text"
                value="{{ isset($data) ? $data->{$role}->dusun : old('dusun') }}" id="dusun" placeholder="Dusun"
                name="dusun" />
            @error('dusun')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="wilayah_id" class="form-label">Wilayah</label>
            <select name="wilayah_id" id="wilayah_id" class="form-select">
                <option value="">Pilih wilayah</option>
                @foreach ($wilayah as $item)
                    <option value="{{ $item->id }}"
                        {{ isset($data) ? ($data->{$role}->wilayah_id == $item->id ? 'selected' : '') : (old('wilayah_id') == $item->id ? 'selected' : '') }}>
                        {{ $item->nama }}
                    </option>
                @endforeach
            </select>
            @error('wilayah_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="kode_pos" class="form-label">Kode Pos</label>
    <input class="form-control @error('kode_pos') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->{$role}->kode_pos : old('kode_pos') }}" id="kode_pos" placeholder="Kode Pos"
        name="kode_pos" />
    @error('kode_pos')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
