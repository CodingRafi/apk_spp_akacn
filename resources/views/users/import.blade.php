@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form action="{{ route('users.saveImport', request('role')) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('users.index', request('role')) }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="text-capitalize mb-0">Import {{ request('role') }}</h5>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="file" class="form-label">File</label>
                                    <input class="form-control @error('file') is-invalid @enderror" type="file"
                                        id="file" name="file" />
                                    @error('file')
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
                                            <option value="{{ $tahun_ajaran->id }}"
                                                {{ isset($data) ? ($data->mahasiswa->tahun_ajaran_id == $tahun_ajaran->id ? 'selected' : '') : '' }}>
                                                {{ $tahun_ajaran->nama }}</option>
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
                                            <option value="{{ $prodi->id }}"
                                                {{ isset($data) ? ($data->mahasiswa->prodi_id == $prodi->id ? 'selected' : '') : '' }}>
                                                {{ $prodi->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
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
