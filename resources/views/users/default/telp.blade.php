<div class="mb-3">
    <label for="telepon" class="form-label">Telepon</label>
    <input class="form-control @error('telepon') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->{request('role')}->telepon : old('telepon') }}" id="telepon"
        placeholder="Kode Pos" name="telepon" />
    @error('telepon')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="handphone" class="form-label">Handphone</label>
    <input class="form-control @error('handphone') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->{request('role')}->handphone : old('handphone') }}" id="handphone"
        placeholder="Handphone" name="handphone" />
    @error('handphone')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>