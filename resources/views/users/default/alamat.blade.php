@php
    $disabled = isset($disabled) ? $disabled : false;
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="kewarganegaraan_id" class="form-label">Kewarganegaraan</label>
        <select class="form-select select2 @error('kewarganegaraan_id') is-invalid @enderror" name="kewarganegaraan_id"
            id="kewarganegaraan_id" onchange="get_wilayah()" {{ $disabled ? 'disabled' : '' }}>
            <option value="">Pilih Kewarganegaraan</option>
            @foreach ($kewarganegaraan as $item)
                <option value="{{ $item->id }}"
                    {{ isset($data) ? ($data->{$role}->kewarganegaraan_id == $item->id ? 'selected' : '') : (old('kewarganegaraan_id') == $item->id ? 'selected' : '') }}>
                    {{ $item->nama }}
                </option>
            @endforeach
        </select>
        @error('kewarganegaraan_id')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="wilayah_id" class="form-label">Wilayah</label>
            <select name="wilayah_id" id="wilayah_id" class="form-select select2" {{ $disabled ? 'disabled' : '' }}>
                <option value="">Pilih wilayah</option>
            </select>
            @error('wilayah_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="rt" class="form-label">RT</label>
            <input class="form-control @error('rt') is-invalid @enderror" type="number"
                value="{{ isset($data) ? $data->{$role}->rt : old('rt') }}" id="rt" placeholder="rt"
                name="rt" {{ $disabled ? 'disabled' : '' }} />
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
                name="rw" {{ $disabled ? 'disabled' : '' }} />
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
        name="jalan" {{ $disabled ? 'disabled' : '' }} />
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
                name="dusun" {{ $disabled ? 'disabled' : '' }} />
            @error('dusun')
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
        name="kode_pos" {{ $disabled ? 'disabled' : '' }} />
    @error('kode_pos')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

<script>
    function get_wilayah(wilayah_id = null) {
        $('#wilayah_id').empty().append(`<option value="">Pilih wilayah</option>`);
        if ($('#kewarganegaraan_id').val()) {
            $.ajax({
                url: "{{ route('get-wilayah') }}",
                data: {
                    'negara_id': $('#kewarganegaraan_id').val()
                },
                success: function(res) {
                    $.each(res.data, function(key, value) {
                        $('#wilayah_id').append(
                            `<option value="${value.id}">${value.nama}</option>`)
                    })

                    $('#wilayah_id').val(wilayah_id)
                },
                error: function(err) {
                    alert('Gagal get wilayah')
                }
            })
        }
    }
</script>

@if (isset($data->{$role}->wilayah_id) || old('wilayah_id'))
    <script>
        get_wilayah('{{ isset($data) ? $data->{$role}->wilayah_id : old('wilayah_id') }}');
    </script>
@endif
