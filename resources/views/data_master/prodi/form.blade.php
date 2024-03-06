@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ isset($data) ? route('data-master.prodi.update', $data->id) : route('data-master.prodi.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('data-master.prodi.index') }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Prodi</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode</label>
                                    <input class="form-control @error('kode') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->kode : old('kode') }}" id="kode"
                                        name="kode" />
                                    @error('kode')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input class="form-control @error('nama') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->nama : old('nama') }}" id="nama"
                                        name="nama" />
                                    @error('nama')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="akreditas" class="form-label">Akreditas</label>
                                    <input class="form-control @error('akreditas') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->akreditas : old('akreditas') }}" id="akreditas"
                                        name="akreditas" />
                                    @error('akreditas')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="jenjang_id" class="form-label">Jenjang</label>
                                    <select name="jenjang_id" id="jenjang_id" class="form-control">
                                        <option value="">Pilih Jenjang</option>
                                        @foreach ($jenjangs as $jenjang)
                                            <option value="{{ $jenjang->id }}"
                                                {{ isset($data) ? ($data->jenjang_id == $jenjang->id ? 'selected' : '') : (old('jenjang_id') == $jenjang->id ? 'selected' : '') }}>
                                                {{ $jenjang->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenjang_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <div class='form-check form-switch'>
                                        <input class='form-check-input' type='checkbox' role='switch' name='status'
                                            value='1' id='status'
                                            {{ isset($data) ? ($data->status == 1 ? 'checked' : '') : (old('status') == 1 ? 'checked' : '') }}>
                                    </div>
                                    @error('status')
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
