@extends('mylayouts.main')

@section('container')
<div class="content-wrapper">

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <form
                action="{{ isset($data) ? route('users.update', ['role' => request('role'), 'id' => $data->id]) : route('users.store', ['role' => request('role')]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($data))
                @method('patch')
                @endif
                <div class="col-xl-12">
                    <!-- HTML5 Inputs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('users.index', request('role')) }}"><i
                                    class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} {{
                                request('role') }}</h5>
                        </div>
                        <div class="card-body">
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
                            @if (request('role') == 'mahasiswa')
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM</label>
                                <input class="form-control @error('nim') is-invalid @enderror" type="text"
                                    value="{{ isset($data) ? $data->mahasiswa->nim : old('nim') }}" id="nim"
                                    placeholder="nim" name="nim" />
                                @error('nim')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
                                    name="tahun_ajaran_id">
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach ($tahun_ajarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}" {{ isset($data) ? ($data->mahasiswa->tahun_ajaran_id == $tahun_ajaran->id ? 'selected' : '') : '' }}>{{ $tahun_ajaran->nama }}</option>
                                    @endforeach
                                </select>
                                @error('tahun_ajaran_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="prodi_id" class="form-label">Prodi</label>
                                <select class="form-select @error('prodi_id') is-invalid @enderror" name="prodi_id">
                                    <option value="">Pilih Prodi</option>
                                    @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ isset($data) ? ($data->mahasiswa->prodi_id == $prodi->id ? 'selected' : '') : '' }}>{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @else
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input class="form-control @error('nip') is-invalid @enderror" type="text"
                                    value="{{ isset($data) ? $data->petugas->nip : old('nip') }}" id="nip"
                                    placeholder="nip" name="nip" />
                                @error('nip')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ttd" class="form-label">Tanda Tangan</label>
                                <div class="d-flex" style="gap: 1rem;">
                                    <input class="form-control @error('ttd') is-invalid @enderror" type="file" id="ttd" name="ttd" />
                                    @if (isset($data))
                                        <a href="{{ asset('storage/'.$data->petugas->ttd) }}" class="btn btn-primary" target="_blank">Lihat</a>
                                    @endif
                                </div>
                                @error('ttd')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @endif
                            @if (!isset($data))
                            <div class="mb-3">
                                <label for="email" class="form-label">Password</label>
                                <input class="form-control" type="text" value="000000" name="number" disabled />
                            </div>
                            @endif
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection