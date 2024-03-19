@php
    $disabled = isset($disabled) ? $disabled : false;
@endphp

<div class="mb-3">
    <img src="{{ asset(Auth::user()->profile ? 'storage/' . Auth::user()->profile : 'image/profile.jpg') }}"
        alt="user-avatar" class="d-block rounded mb-3" height="200" width="200" id="uploadedAvatar"
        style="object-fit: cover;" />

    @if (!$disabled)
    <input class="form-control mb-3 input-pp @error('profile') is-invalid @enderror" type="file" name="profile"
        id="foto" accept="image/*" onchange="previewImageUpdate();" />
    @endif
</div>
<div class="mb-3">
    <label for="name" class="form-label">Nama</label>
    <input class="form-control @error('name') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->name : old('name') }}" id="name" placeholder="Name User" name="name" {{ $disabled ? 'disabled' : '' }} />
    @error('name')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('email') is-invalid @enderror" type="text"
        value="{{ isset($data) ? $data->email : old('email') }}" id="email" placeholder="Email" name="email" />
    @error('email')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
@if (!isset($data))
    <div class="mb-3">
        <label for="email" class="form-label">Password</label>
        <input class="form-control" type="text" value="000000" name="number" disabled />
    </div>
@endif
<script>
    function previewImageUpdate() {
        const pp_preview = document.querySelector('#uploadedAvatar');
        const input = document.querySelector('.input-pp');

        pp_preview.style.display = 'block';

        var oFReader = new FileReader();
        oFReader.readAsDataURL(input.files[0]);

        oFReader.onload = function(oFREvent) {
            pp_preview.src = oFREvent.target.result;
        };
    };
</script>
