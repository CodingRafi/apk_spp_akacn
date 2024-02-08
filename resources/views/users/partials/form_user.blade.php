<div class="mb-3">
    <label for="name" class="form-label">Nama</label>
    <input class="form-control @error('name') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->name : old('name') }}" id="name"
        placeholder="Name User" name="name" />
    @error('name')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input class="form-control @error('email') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->email : old('email') }}" id="email"
        placeholder="Email" name="email" />
    @error('email')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
@if (!isset($data))
<div class="mb-3">
    <label for="email" class="form-label">Password</label>
    <input class="form-control" type="text" value="000000" name="number"
        disabled />
</div>
@endif