@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ isset($data) ? route('kelola-users.mahasiswa.update', ['role' => request('role'), 'id' => $data->id]) : route('kelola-users.mahasiswa.store', ['role' => request('role')]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('kelola-users.mahasiswa.index', request('role')) }}"><i
                                    class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} {{ request('role') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="tab-main">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link active a-tab" href="#identitas">Identitas</a>
                                    </li>
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link a-tab" href="#ayah">Ayah</a>
                                    </li>
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link a-tab" href="#ibu">Ibu</a>
                                    </li>
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link a-tab" href="#wali">Wali</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content py-4 px-1">
                                <div class="tab-pane active" id="identitas" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @include('users.form_user')
                                            <div class="mb-3">
                                                <label for="nim" class="form-label">NIM</label>
                                                <input class="form-control @error('nim') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->nim : old('nim') }}"
                                                    id="nim" placeholder="NIM" name="nim" />
                                                @error('nim')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nisn" class="form-label">NISN</label>
                                                <input class="form-control @error('nisn') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->nisn : old('nisn') }}"
                                                    id="nisn" placeholder="NISN" name="nisn" />
                                                @error('nisn')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nik" class="form-label">NIK</label>
                                                <input class="form-control @error('nik') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->nik : old('nik') }}"
                                                    id="nik" placeholder="NIK" name="nik" />
                                                @error('nik')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                                <input class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->tempat_lahir : old('tempat_lahir') }}"
                                                    id="tempat_lahir" placeholder="Tempat Lahir" name="tempat_lahir" />
                                                @error('tempat_lahir')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                                <input class="form-control @error('tgl_lahir') is-invalid @enderror"
                                                    type="date"
                                                    value="{{ isset($data) ? $data->mahasiswa->tgl_lahir : old('tgl_lahir') }}"
                                                    id="tgl_lahir" placeholder="Tempat Lahir" name="tgl_lahir" />
                                                @error('tgl_lahir')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="jk" class="form-label">Jenis Kelamin</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="jk"
                                                            value="l" id="jk-l">
                                                        <label class="form-check-label" for="jk-l">
                                                            Laki-laki
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="jk"
                                                            value="p" id="jk-p">
                                                        <label class="form-check-label" for="jk-p">
                                                            Perempuan
                                                        </label>
                                                    </div>
                                                </div>
                                                @error('jk')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="rt" class="form-label">RT</label>
                                                        <input class="form-control @error('rt') is-invalid @enderror"
                                                            type="text"
                                                            value="{{ isset($data) ? $data->mahasiswa->rt : old('rt') }}"
                                                            id="rt" placeholder="rt" name="rt" />
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
                                                        <input class="form-control @error('rw') is-invalid @enderror"
                                                            type="text"
                                                            value="{{ isset($data) ? $data->mahasiswa->rw : old('rw') }}"
                                                            id="rw" placeholder="rw" name="rw" />
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
                                                <input class="form-control @error('jalan') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->jalan : old('jalan') }}"
                                                    id="jalan" placeholder="Jalan" name="jalan" />
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
                                                        <input class="form-control @error('dusun') is-invalid @enderror"
                                                            type="text"
                                                            value="{{ isset($data) ? $data->mahasiswa->dusun : old('dusun') }}"
                                                            id="dusun" placeholder="Dusun" name="dusun" />
                                                        @error('dusun')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                                        <input
                                                            class="form-control @error('kelurahan') is-invalid @enderror"
                                                            type="text"
                                                            value="{{ isset($data) ? $data->mahasiswa->kelurahan : old('kelurahan') }}"
                                                            id="kelurahan" placeholder="kelurahan" name="kelurahan" />
                                                        @error('kelurahan')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                                <input class="form-control @error('kode_pos') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->kode_pos : old('kode_pos') }}"
                                                    id="kode_pos" placeholder="Kode Pos" name="kode_pos" />
                                                @error('kode_pos')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="telepon" class="form-label">Telepon</label>
                                                <input class="form-control @error('telepon') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->telepon : old('telepon') }}"
                                                    id="telepon" placeholder="Kode Pos" name="telepon" />
                                                @error('telepon')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="handphone" class="form-label">Handphone</label>
                                                <input class="form-control @error('handphone') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->handphone : old('handphone') }}"
                                                    id="handphone" placeholder="Kode Pos" name="Handphone" />
                                                @error('handphone')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="jk" class="form-label">Penerima KPS</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="penerima_kps" value="1" id="kps_1">
                                                        <label class="form-check-label" for="kps_1">
                                                            Ya
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="penerima_kps" value="0" id="kps_0" checked>
                                                        <label class="form-check-label" for="kps_0">
                                                            Tidak
                                                        </label>
                                                    </div>
                                                </div>
                                                @error('jk')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="no_kps" class="form-label">No KPS</label>
                                                <input class="form-control @error('no_kps') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->no_kps : old('no_kps') }}"
                                                    id="no_kps" placeholder="Kode Pos" name="No KPS" />
                                                @error('no_kps')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="npwp" class="form-label">NPWP</label>
                                                <input class="form-control @error('npwp') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->mahasiswa->npwp : old('npwp') }}"
                                                    id="npwp" placeholder="Kode Pos" name="NPWP" />
                                                @error('npwp')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="rombel_id" class="form-label">Rombel</label>
                                                <select class="form-select @error('rombel_id') is-invalid @enderror"
                                                    name="rombel_id">
                                                    <option value="">Pilih Rombel</option>
                                                </select>
                                                @error('rombel_id')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="jk" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="status" id="publish"
                                                        {{ isset($data) ? ($data->status ? 'checked' : '') : old('status') }}>
                                                </div>
                                                @error('jk')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="tahun_ajaran_id" class="form-label">Tahun Masuk</label>
                                                <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
                                                    name="tahun_ajaran_id">
                                                    <option value="">Pilih Tahun Masuk</option>
                                                    @foreach ($tahun_ajarans as $tahun_ajaran)
                                                        <option value="{{ $tahun_ajaran->id }}"
                                                            {{ isset($data) ? ($data->mahasiswa->tahun_ajaran_id == $tahun_ajaran->id ? 'selected' : '') : '' }}>
                                                            {{ $tahun_ajaran->tahun_mulai }}/{{ $tahun_ajaran->tahun_akhir }}
                                                            {{ $tahun_ajaran->semester == 1 ? 'Ganjil' : 'Genap' }}
                                                        </option>
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
                                                <select class="form-select @error('prodi_id') is-invalid @enderror"
                                                    name="prodi_id">
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
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="ayah" role="tabpanel">
                                    @include('users.mahasiswa.form_ortu', ['value' => 'ayah'])
                                </div>

                                <div class="tab-pane" id="ibu" role="tabpanel">
                                    @include('users.mahasiswa.form_ortu', ['value' => 'ibu'])
                                </div>

                                <div class="tab-pane" id="wali" role="tabpanel">
                                    @include('users.mahasiswa.form_ortu', ['value' => 'wali'])
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
