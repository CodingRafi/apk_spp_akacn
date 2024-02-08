@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ isset($data) ? route('data-master.tahun-ajaran.update', $data->id) : route('data-master.tahun-ajaran.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="col-xl-12">
                        <!-- HTML5 Inputs -->
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('data-master.tahun-ajaran.index') }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Tahun Ajaran</h5>
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
                                    <label for="tahun_mulai" class="form-label">Tahun Mulai</label>
                                    <input class="form-control @error('tahun_mulai') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->tahun_mulai : old('tahun_mulai') }}"
                                        id="tahun_mulai" name="tahun_mulai" />
                                    @error('tahun_mulai')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_akhir" class="form-label">Tahun Akhir</label>
                                    <input class="form-control @error('tahun_akhir') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->tahun_akhir : old('tahun_akhir') }}"
                                        id="tahun_akhir" name="tahun_akhir" />
                                    @error('tahun_akhir')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-select select2 @error('semester') is-invalid @enderror"
                                        name="semester">
                                        <option value="">Pilih Semester</option>
                                        <option value="1" {{ isset($data) ? ($data->semester == '1' ? 'selected' : '') : old('semester') }}>Ganjil</option>
                                        <option value="2" {{ isset($data) ? ($data->semester == '2' ? 'selected' : '') : old('semester') }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="status"
                                            id="status"
                                            {{ isset($data) ? ($data->status ? 'checked' : '') : old('status') }}>
                                    </div>
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
