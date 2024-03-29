@php
    $page = isset($page) ? $page : 'form';
    $role = $page == 'form' ? request('role') : getRole()->name;
    $disabled = isset($disabled) ? $disabled : false;
@endphp
<div id="tab-main">
    <ul class="nav nav-tabs" id="profile">
        <li class="nav-item" style="white-space: nowrap;">
            <button type="button" data-bs-toggle="tab" class="nav-link active a-tab"
                data-bs-target="#identitas">Identitas</button>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
            <button type="button" data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#status">Status</button>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                data-bs-target="#lainnya">Lainnya</button>
        </li>
    </ul>
</div>

<div class="tab-content py-4 px-1" id="profileContent">
    <div class="tab-pane active" id="identitas" role="tabpanel">
        <div class="row">
            <div class="col-md-6">
                @include('users.form_user')
                <div class="mb-3">
                    <label for="nip" class="form-label">NIP</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('nip') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->dosen->nip : old('nip') }}" id="nip"
                        placeholder="NIP" name="nip" {{ $disabled ? 'disabled' : '' }} />
                    @error('nip')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @include('users.default.identitas', [
                    'role' => $role,
                    'page' => $page,
                ])
                <div class="mb-3">
                    <label for="login_key" class="form-label">NIDN</label>
                    <input {{ $disabled ? 'disabled' : '' }}
                        class="form-control @error('login_key') is-invalid @enderror" type="text"
                        value="{{ isset($data) ? $data->login_key : old('login_key') }}" id="login_key"
                        placeholder="login_key" name="login_key" />
                    @error('login_key')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nama_ibu" class="form-label">Nama Ibu</label>
                    <input {{ $disabled ? 'disabled' : '' }}
                        class="form-control @error('nama_ibu') is-invalid @enderror" type="text"
                        value="{{ isset($data) ? $data->dosen->nama_ibu : old('nama_ibu') }}" id="nama_ibu"
                        placeholder="Nama Ibu" name="nama_ibu" />
                    @error('nama_ibu')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('nik') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->dosen->nik : old('nik') }}" id="nik"
                        placeholder="NIK" name="nik" />
                    @error('nik')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('npwp') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->dosen->npwp : old('npwp') }}" id="npwp"
                        placeholder="NPWP" name="npwp" />
                    @error('npwp')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @include('users.default.alamat', [
                    'role' => $role,
                ])
                @include('users.default.telp', [
                    'role' => $role,
                ])
                <div class="mb-3">
                    <label for="status_pernikahan" class="form-label">Status
                        Pernikahan</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="status_pernikahan" value="1" id="status_pernikahan-1"
                                {{ isset($data) ? ($data->dosen->status_pernikahan == 1 ? 'checked' : '') : (old('status_pernikahan') == 1 ? 'checked' : '') }}>
                            <label class="form-check-label" for="status_pernikahan-1">
                                Sudah
                            </label>
                        </div>
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="status_pernikahan" value="0" id="status_pernikahan-0"
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
                    <input {{ $disabled ? 'disabled' : '' }}
                        class="form-control @error('nominal_tunjangan') is-invalid @enderror" type="number"
                        value="{{ isset($data) ? $data->dosen->nominal_tunjangan : old('nominal_tunjangan') }}"
                        id="nominal_tunjangan" placeholder="Nominal Tunjangan" name="nominal_tunjangan"
                        {{ $page == 'profile' ? 'disabled' : '' }} />
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
            <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('no_sk_cpns') is-invalid @enderror"
                type="text" value="{{ isset($data) ? $data->dosen->no_sk_cpns : old('no_sk_cpns') }}"
                id="no_sk_cpns" placeholder="No SK CPNS" name="no_sk_cpns" />
            @error('no_sk_cpns')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="tgl_sk_cpns" class="form-label">Tanggal SK CPNS</label>
            <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('tgl_sk_cpns') is-invalid @enderror"
                type="date" value="{{ isset($data) ? $data->dosen->tgl_sk_cpns : old('tgl_sk_cpns') }}"
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
            <input {{ $disabled ? 'disabled' : '' }}
                class="form-control @error('no_sk_pengangkatan') is-invalid @enderror" type="text"
                value="{{ isset($data) ? $data->dosen->no_sk_pengangkatan : old('no_sk_pengangkatan') }}"
                id="no_sk_pengangkatan" placeholder="No SK Pengangkatan" name="no_sk_pengangkatan" />
            @error('no_sk_pengangkatan')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="mulai_sk_pengangkatan" class="form-label">Mulai SK
                Pengangkatan</label>
            <input {{ $disabled ? 'disabled' : '' }}
                class="form-control @error('mulai_sk_pengangkatan') is-invalid @enderror" type="date"
                value="{{ isset($data) ? $data->dosen->mulai_sk_pengangkatan : old('mulai_sk_pengangkatan') }}"
                id="mulai_sk_pengangkatan" placeholder="Mulai SK Pengangkatan" name="mulai_sk_pengangkatan" />
            @error('mulai_sk_pengangkatan')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="pangkat_golongan_id" class="form-label">Pangkat Golongan</label>
            <select name="pangkat_golongan_id" id="pangkat_golongan_id" class="form-control @error('pangkat_golongan_id') is-invalid @enderror" {{ $disabled ? 'disabled' : '' }}>
                <option value="">Pilih Pangkat Golongan</option>
                @foreach ($pangkatGolongan as $pangkat)
                <option value="{{ $pangkat->id }}" {{ isset($data) ? ($data->dosen->pangkat_golongan_id == $pangkat->id ? 'selected' : '') : (old('pangkat_golongan_id') == $pangkat->id ? 'selected' : '') }}>{{ $pangkat->nama }}</option>
                @endforeach
            </select>
            @error('pangkat_golongan_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="lembaga_pengangkat_id" class="form-label">Lembaga Pengangkatan</label>
            <select name="lembaga_pengangkat_id" id="lembaga_pengangkat_id" class="form-control @error('lembaga_pengangkat_id') is-invalid @enderror" {{ $disabled ? 'disabled' : '' }}>
                <option value="">Pilih Lembaga Pengangkat</option>
                @foreach ($lembagaPengangkat as $lembaga)
                <option value="{{ $lembaga->id }}" {{ isset($data) ? ($data->dosen->lembaga_pengangkat_id == $lembaga->id ? 'selected' : '') : (old('lembaga_pengangkat_id') == $lembaga->id ? 'selected' : '') }}>{{ $lembaga->nama }}</option>
                @endforeach
            </select>
            @error('lembaga_pengangkat_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="tgl_mulai_pns" class="form-label">Tanggal mulai PNS</label>
            <input {{ $disabled ? 'disabled' : '' }}
                class="form-control @error('tgl_mulai_pns') is-invalid @enderror" type="date"
                value="{{ isset($data) ? $data->dosen->tgl_mulai_pns : old('tgl_mulai_pns') }}" id="tgl_mulai_pns"
                placeholder="No SK CPNS" name="tgl_mulai_pns" />
            @error('tgl_mulai_pns')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="tab-pane" id="lainnya" role="tabpanel">
        @include('users.default.ngajar', [
            'role' => $role,
            'disabled' => $disabled,
        ])
    </div>
</div>

@if (!$disabled)
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
@endif
