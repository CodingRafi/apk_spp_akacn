@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ isset($data) ? route('kelola-users.dosen.update', $data->id) : route('kelola-users.dosen.store', ['role' => request('role')]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('kelola-users.dosen.index', request('role')) }}"><i
                                    class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Dosen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="tab-main">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link active a-tab" href="#identitas">Identitas</a>
                                    </li>
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link a-tab" href="#status">Status</a>
                                    </li>
                                    <li class="nav-item" style="white-space: nowrap;">
                                        <a class="nav-link a-tab" href="#lainnya">Lainnya</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content py-4 px-1">
                                <div class="tab-pane active" id="identitas" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @include('users.form_user')
                                            <div class="mb-3">
                                                <label for="login_key" class="form-label">NIP</label>
                                                <input class="form-control @error('login_key') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->login_key : old('login_key') }}"
                                                    id="login_key" placeholder="NIP" name="login_key" />
                                                @error('login_key')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                                <input class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->tempat_lahir : old('tempat_lahir') }}"
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
                                                    value="{{ isset($data) ? $data->dosen->tgl_lahir : old('tgl_lahir') }}"
                                                    id="tgl_lahir" placeholder="Tempat Lahir" name="tgl_lahir" />
                                                @error('tgl_lahir')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="agama_id" class="form-label">Agama</label>
                                                <select class="form-select @error('agama_id') is-invalid @enderror"
                                                    name="agama_id">
                                                    <option value="">Pilih Agama</option>
                                                    @foreach ($agamas as $agama)
                                                        <option value="{{ $agama->id }}"
                                                            {{ isset($data) ? ($data->dosen->agama_id == $agama->id ? 'selected' : '') : (old('agama_id') == $agama->id ? 'selected' : '') }}>
                                                            {{ $agama->nama }}</option>
                                                    @endforeach
                                                </select>
                                                @error('agama_id')
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
                                                        {{ isset($data) ? ($data->dosen->status ? 'checked' : '') : (old('status') ? 'checked' : '') }}>
                                                </div>
                                                @error('jk')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nidn" class="form-label">NIDN</label>
                                                <input class="form-control @error('nidn') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->nidn : old('nidn') }}"
                                                    id="nidn" placeholder="NIDN" name="nidn" />
                                                @error('nidn')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                                <input class="form-control @error('nama_ibu') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->nama_ibu : old('nama_ibu') }}"
                                                    id="nama_ibu" placeholder="Nama Ibu" name="nama_ibu" />
                                                @error('nama_ibu')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nik" class="form-label">NIK</label>
                                                <input class="form-control @error('nik') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->nik : old('nik') }}"
                                                    id="nik" placeholder="NIK" name="nik" />
                                                @error('nik')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="npwp" class="form-label">NPWP</label>
                                                <input class="form-control @error('npwp') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->npwp : old('npwp') }}"
                                                    id="npwp" placeholder="NPWP" name="npwp" />
                                                @error('npwp')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="jk" class="form-label">Jenis Kelamin</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="jk"
                                                            value="l" id="jk-l"
                                                            {{ isset($data) ? ($data->dosen->jk == 'l' ? 'checked' : '') : (old('jk') == 'l' ? 'checked' : '') }}>
                                                        <label class="form-check-label" for="jk-l">
                                                            Laki-laki
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="jk"
                                                            value="p" id="jk-p"
                                                            {{ isset($data) ? ($data->dosen->jk == 'p' ? 'checked' : '') : (old('jk') == 'p' ? 'checked' : '') }}>
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
                                                            type="number"
                                                            value="{{ isset($data) ? $data->dosen->rt : old('rt') }}"
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
                                                            type="number"
                                                            value="{{ isset($data) ? $data->dosen->rw : old('rw') }}"
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
                                                    value="{{ isset($data) ? $data->dosen->jalan : old('jalan') }}"
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
                                                            value="{{ isset($data) ? $data->dosen->dusun : old('dusun') }}"
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
                                                        <label for="wilayah_id" class="form-label">Wilayah</label>
                                                        <select name="wilayah_id" id="wilayah_id" class="form-select">
                                                            <option value="">Pilih wilayah</option>
                                                            @foreach ($wilayah as $item)
                                                                <option value="{{ $item->id }}"
                                                                    {{ isset($data) ? ($data->dosen->wilayah_id == $item->id ? 'selected' : '') : old('wilayah_id') }}>
                                                                    {{ $item->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('wilayah_id')
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
                                                    value="{{ isset($data) ? $data->dosen->kode_pos : old('kode_pos') }}"
                                                    id="kode_pos" placeholder="Kode Pos" name="kode_pos" />
                                                @error('kode_pos')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="telepon" class="form-label">Telepon</label>
                                                <input class="form-control @error('telepon') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->telepon : old('telepon') }}"
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
                                                    value="{{ isset($data) ? $data->dosen->handphone : old('handphone') }}"
                                                    id="handphone" placeholder="Handphone" name="handphone" />
                                                @error('handphone')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="status_pernikahan" class="form-label">Status
                                                    Pernikahan</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="status_pernikahan" value="1"
                                                            id="status_pernikahan-1"
                                                            {{ isset($data) ? ($data->dosen->status_pernikahan == 1 ? 'checked' : '') : (old('status_pernikahan') == 1 ? 'checked' : '') }}>
                                                        <label class="form-check-label" for="status_pernikahan-1">
                                                            Sudah
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="status_pernikahan" value="0"
                                                            id="status_pernikahan-0"
                                                            {{ isset($data) ? ($data->dosen->status_pernikahan == 0 ? 'checked' : '') : (old('status_pernikahan') == 0 ? 'checked' : '') }}>
                                                        <label class="form-check-label" for="status_pernikahan-0">
                                                            Belum
                                                        </label>
                                                    </div>
                                                </div>
                                                @error('status_pernikahan')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nominal_tunjangan" class="form-label">Nominal
                                                    Tunjangan</label>
                                                <input
                                                    class="form-control @error('nominal_tunjangan') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ isset($data) ? $data->dosen->nominal_tunjangan : old('nominal_tunjangan') }}"
                                                    id="nominal_tunjangan" placeholder="Nominal Tunjangan"
                                                    name="nominal_tunjangan" />
                                                @error('nominal_tunjangan')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="status" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="no_sk_cpns" class="form-label">No SK CPNS</label>
                                        <input class="form-control @error('no_sk_cpns') is-invalid @enderror"
                                            type="text"
                                            value="{{ isset($data) ? $data->dosen->no_sk_cpns : old('no_sk_cpns') }}"
                                            id="no_sk_cpns" placeholder="No SK CPNS" name="no_sk_cpns" />
                                        @error('no_sk_cpns')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="tgl_sk_cpns" class="form-label">Tanggal SK CPNS</label>
                                        <input class="form-control @error('tgl_sk_cpns') is-invalid @enderror"
                                            type="date"
                                            value="{{ isset($data) ? $data->dosen->tgl_sk_cpns : old('tgl_sk_cpns') }}"
                                            id="tgl_sk_cpns" placeholder="No SK CPNS" name="tgl_sk_cpns" />
                                        @error('tgl_sk_cpns')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="no_sk_pengangkatan" class="form-label">No SK
                                            Pengangkatan</label>
                                        <input class="form-control @error('no_sk_pengangkatan') is-invalid @enderror"
                                            type="text"
                                            value="{{ isset($data) ? $data->dosen->no_sk_pengangkatan : old('no_sk_pengangkatan') }}"
                                            id="no_sk_pengangkatan" placeholder="No SK Pengangkatan"
                                            name="no_sk_pengangkatan" />
                                        @error('no_sk_pengangkatan')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="mulai_sk_pengangkatan" class="form-label">Mulai SK
                                            Pengangkatan</label>
                                        <input class="form-control @error('mulai_sk_pengangkatan') is-invalid @enderror"
                                            type="date"
                                            value="{{ isset($data) ? $data->dosen->mulai_sk_pengangkatan : old('mulai_sk_pengangkatan') }}"
                                            id="mulai_sk_pengangkatan" placeholder="Mulai SK Pengangkatan"
                                            name="mulai_sk_pengangkatan" />
                                        @error('mulai_sk_pengangkatan')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="tgl_mulai_pns" class="form-label">Tanggal mulai PNS</label>
                                        <input class="form-control @error('tgl_mulai_pns') is-invalid @enderror"
                                            type="date"
                                            value="{{ isset($data) ? $data->dosen->tgl_mulai_pns : old('tgl_mulai_pns') }}"
                                            id="tgl_mulai_pns" placeholder="No SK CPNS" name="tgl_mulai_pns" />
                                        @error('tgl_mulai_pns')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="tab-pane" id="lainnya" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="mampu_handle_kebutuhan_khusus" class="form-label">Mampu handle
                                            Kebutuhan
                                            Khusus?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_khusus" value="1"
                                                    id="mampu_handle_kebutuhan_khusus-1"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_khusus == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_khusus') == 1 ? 'checked' : '') }}>
                                                <label class="form-check-label" for="mampu_handle_kebutuhan_khusus-1">
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_khusus" value="0"
                                                    id="mampu_handle_kebutuhan_khusus-0"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_khusus == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_khusus') == 0 ? 'checked' : '') }}>
                                                <label class="form-check-label" for="mampu_handle_kebutuhan_khusus-0">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                        @error('mampu_handle_kebutuhan_khusus')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="mampu_handle_kebutuhan_braille" class="form-label">Mampu
                                            handleKebutuhan
                                            braille?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_braille" value="1"
                                                    id="mampu_handle_kebutuhan_braille-1"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_braille == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_braille') == 1 ? 'checked' : '') }}>
                                                <label class="form-check-label" for="mampu_handle_kebutuhan_braille-1">
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_braille" value="0"
                                                    id="mampu_handle_kebutuhan_braille-0"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_braille == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_braille') == 0 ? 'checked' : '') }}>
                                                <label class="form-check-label" for="mampu_handle_kebutuhan_braille-0">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                        @error('mampu_handle_kebutuhan_braille')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="mampu_handle_kebutuhan_bahasa_isyarat" class="form-label">Mampu handle
                                            Kebutuhan
                                            bahasa isyarat?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_bahasa_isyarat" value="1"
                                                    id="mampu_handle_kebutuhan_bahasa_isyarat-1"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_bahasa_isyarat == 1 ? 'checked' : '') : (old('mampu_handle_kebutuhan_bahasa_isyarat') == 1 ? 'checked' : '') }}>
                                                <label class="form-check-label"
                                                    for="mampu_handle_kebutuhan_bahasa_isyarat-1">
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="mampu_handle_kebutuhan_bahasa_isyarat" value="0"
                                                    id="mampu_handle_kebutuhan_bahasa_isyarat-0"
                                                    {{ isset($data) ? ($data->dosen->mampu_handle_kebutuhan_bahasa_isyarat == 0 ? 'checked' : '') : (old('mampu_handle_kebutuhan_bahasa_isyarat') == 0 ? 'checked' : '') }}>
                                                <label class="form-check-label"
                                                    for="mampu_handle_kebutuhan_bahasa_isyarat-0">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                        @error('mampu_handle_kebutuhan_bahasa_isyarat')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
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
