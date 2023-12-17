@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4" style="border-top: 10px solid #1e88d7">
                        <h5 class="card-header">Profile Details</h5>

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-3">
                                    <div class="card-body d-flex justify-content-center" style="flex-direction: column">
                                        <div class="d-flex justify-content-center gap-4">
                                            <img src="{{ asset(Auth::user()->profile ? 'storage/' . Auth::user()->profile : 'image/profile.jpg') }}"
                                                alt="user-avatar" class="d-block rounded" height="200" width="200"
                                                id="uploadedAvatar" style="object-fit: cover;" />
                                        </div>
                                        <a href="/ubah-password" class="btn btn-primary mt-3">Ubah Password</a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card-body">
                                        <form id="formAccountSettings" method="POST" enctype="multipart/form-data"
                                            action="{{ route('profile.update') }}">
                                            @csrf
                                            @method('patch')
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input class="form-control @error('name') is-invalid @enderror"
                                                        type="text" id="name" name="name"
                                                        value="{{ Auth::user()->name }}" autofocus />
                                                    @error('name')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input class="form-control @error('email') is-invalid @enderror"
                                                        type="email" name="email" id="email"
                                                        value="{{ Auth::user()->email }}" />
                                                    @error('email')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="foto" class="form-label">Profil Picture</label>
                                                    <input
                                                        class="form-control input-pp @error('profile') is-invalid @enderror"
                                                        type="file" name="profile" id="foto" accept="image/*"
                                                        onchange="previewImageUpdate();" />
                                                    @error('profile')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                @if (Auth::user()->hasRole('mahasiswa'))
                                                    <div class="mb-3 col-md-6">
                                                        <label for="nim" class="form-label">NIM</label>
                                                        <input class="form-control @error('nim') is-invalid @enderror"
                                                            type="text" name="nim" id="nim"
                                                            value="{{ Auth::user()->mahasiswa->nim }}" />
                                                        @error('nim')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                @else
                                                    <div class="mb-3 col-md-6">
                                                        <label for="nip" class="form-label">NIP</label>
                                                        <input class="form-control @error('nip') is-invalid @enderror"
                                                            type="text" name="nip" id="nip"
                                                            value="{{ Auth::user()->petugas->nip }}" />
                                                        @error('nip')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="foto" class="form-label">Tanda Tangan</label>
                                                        <div class="d-flex justify-content-center align-items-center"
                                                            style="gap: 1rem;">
                                                            <input
                                                                class="form-control input-pp @error('ttd') is-invalid @enderror"
                                                                type="file" name="ttd" id="foto"
                                                                accept="image/*" />
                                                            @if (Auth::user()->petugas->ttd)
                                                                <a href="{{ asset('storage/' . Auth::user()->petugas->ttd) }}"
                                                                    class="btn btn-primary" target="_blank">Lihat</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
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
@endpush
