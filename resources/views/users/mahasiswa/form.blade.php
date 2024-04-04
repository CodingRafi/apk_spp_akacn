@php
    $page = isset($page) ? $page : 'form';
    $role = $page == 'form' ? (isset($role) ? $role : request('role')) : getRole()->name;
    $disabled = isset($disabled) ? $disabled : false;
    $countPembayaran = isset($countPembayaran) ? $countPembayaran : 0;
    $countKrs = isset($countKrs) ? $countKrs : 0;
@endphp

<ul class="nav nav-tabs" id="profile">
    <li class="nav-item" style="white-space: nowrap;">
        <button data-bs-toggle="tab" class="nav-link active a-tab" data-bs-target="#identitas">Identitas</button>
    </li>
    <li class="nav-item" style="white-space: nowrap;">
        <button data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#ayah">Ayah</button>
    </li>
    <li class="nav-item" style="white-space: nowrap;">
        <button data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#ibu">Ibu</button>
    </li>
    <li class="nav-item" style="white-space: nowrap;">
        <button data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#wali">Wali</button>
    </li>
    <li class="nav-item" style="white-space: nowrap;">
        <button data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#lainnya">Lainnya</button>
    </li>
</ul>

<div class="tab-content py-4 px-1" id="profileContent">
    <div class="tab-pane active" id="identitas" role="tabpanel">
        <div class="row">
            <div class="col-md-6">
                @include('users.form_user', [
                    'disabled' => $disabled,
                ])
                <div class="mb-3">
                    <label for="login_key" class="form-label">NIM</label>
                    <input {{ $disabled ? 'disabled' : '' }}
                        class="form-control @error('login_key') is-invalid @enderror" type="text"
                        value="{{ isset($data) ? $data->login_key : old('login_key') }}" id="login_key"
                        placeholder="NIM" name="login_key" {{ $page == 'profile' ? 'disabled' : '' }} />
                    @error('login_key')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('nisn') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->mahasiswa->nisn : old('nisn') }}" id="nisn"
                        placeholder="NISN" name="nisn" />
                    @error('nisn')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('nik') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->mahasiswa->nik : old('nik') }}" id="nik"
                        placeholder="NIK" name="nik" />
                    @error('nik')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @include('users.default.identitas', [
                    'role' => $role,
                    'page' => $page,
                    'disabled' => $disabled,
                ])
                @include('users.default.telp', [
                    'role' => $role,
                    'disabled' => $disabled,
                ])
            </div>
            <div class="col-md-6">
                @include('users.default.alamat', [
                    'role' => $role,
                ])
                <div class="mb-3">
                    <label for="kelurahan" class="form-label">Kelurahan</label>
                    <input {{ $disabled ? 'disabled' : '' }} {{ $disabled ? 'disabled' : '' }}
                        class="form-control @error('kelurahan') is-invalid @enderror" type="text"
                        value="{{ isset($data) ? $data->mahasiswa->kelurahan : old('kelurahan') }}" id="kelurahan"
                        placeholder="Kelurahan" name="kelurahan" />
                    @error('kelurahan')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="penerima_kps" class="form-label">Penerima KPS</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="penerima_kps" value="1" id="kps_1">
                            <label class="form-check-label" for="kps_1">
                                Ya
                            </label>
                        </div>
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="penerima_kps" value="0" id="kps_0" checked>
                            <label class="form-check-label" for="kps_0">
                                Tidak
                            </label>
                        </div>
                    </div>
                    @error('penerima_kps')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="no_kps" class="form-label">No KPS</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('no_kps') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->mahasiswa->no_kps : old('no_kps') }}"
                        id="no_kps" placeholder="No KPS" name="no_kps" />
                    @error('no_kps')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input {{ $disabled ? 'disabled' : '' }} class="form-control @error('npwp') is-invalid @enderror"
                        type="text" value="{{ isset($data) ? $data->mahasiswa->npwp : old('npwp') }}"
                        id="npwp" placeholder="NPWP" name="npwp" />
                    @error('npwp')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="tahun_masuk_id" class="form-label">Tahun Masuk</label>
                    <select class="form-select select2 @error('tahun_masuk_id') is-invalid @enderror"
                        name="tahun_masuk_id" id="tahun_masuk_id"
                        {{ $page == 'profile' || $disabled || $countPembayaran > 0 || $countKrs > 0 ? 'disabled' : '' }}>
                        <option value="">Pilih Tahun Masuk</option>
                        @foreach ($tahun_ajarans as $tahun_ajaran)
                            <option value="{{ $tahun_ajaran->id }}"
                                {{ isset($data) ? ($data->mahasiswa->tahun_masuk_id == $tahun_ajaran->id ? 'selected' : '') : (old('tahun_masuk_id') == $tahun_ajaran->id ? 'selected' : '') }}>
                                {{ $tahun_ajaran->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('tahun_masuk_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select class="form-select select2 @error('semester_id') is-invalid @enderror" name="semester_id"
                        id="semester_id"
                        {{ $page == 'profile' || $disabled || $countPembayaran > 0 || $countKrs > 0 ? 'disabled' : '' }}>
                        <option value="">Pilih Semester</option>
                    </select>
                    @error('semester_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="prodi_id" class="form-label">Prodi</label>
                    <select class="form-select @error('prodi_id') is-invalid @enderror" name="prodi_id"
                        id="prodi_id"
                        {{ $page == 'profile' || $disabled || $countPembayaran > 0 || $countKrs > 0 ? 'disabled' : '' }}>
                        <option value="">Pilih Prodi</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}"
                                {{ isset($data) ? ($data->mahasiswa->prodi_id == $prodi->id ? 'selected' : '') : (old('prodi_id') == $prodi->id ? 'selected' : '') }}>
                                {{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="jenis_kelas_id" class="form-label">Jenis Kelas</label>
                    <select class="form-select @error('jenis_kelas_id') is-invalid @enderror" name="jenis_kelas_id"
                        id="jenis_kelas_id"
                        {{ $page == 'profile' || $disabled || $countPembayaran > 0 || $countKrs > 0 ? 'disabled' : '' }}>
                        <option value="">Pilih Jenis Kelas</option>
                        @foreach ($jenisKelas as $item)
                            <option value="{{ $item->id }}"
                                {{ isset($data) ? ($data->mahasiswa->jenis_kelas_id == $item->id ? 'selected' : '') : (old('jenis_kelas_id') == $item->id ? 'selected' : '') }}>
                                {{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @error('jenis_kelas_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="rombel_id" class="form-label">Rombel</label>
                    <select class="form-select @error('rombel_id') is-invalid @enderror" name="rombel_id"
                        id="rombel_id"
                        {{ $page == 'profile' || $disabled || $countPembayaran > 0 || $countKrs > 0 ? 'disabled' : '' }}>
                        <option value="">Pilih Rombel</option>
                    </select>
                    @error('rombel_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="kebutuhan_khusus" class="form-label">Kebutuhan Khusus?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="mhs_kebutuhan_khusus" value="1" id="kps_1">
                            <label class="form-check-label" for="kps_1">
                                Ya
                            </label>
                        </div>
                        <div class="form-check">
                            <input {{ $disabled ? 'disabled' : '' }} class="form-check-input" type="radio"
                                name="mhs_kebutuhan_khusus" value="0" id="kps_0" checked>
                            <label class="form-check-label" for="kps_0">
                                Tidak
                            </label>
                        </div>
                    </div>
                    @error('mhs_kebutuhan_khusus')
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

    <div class="tab-pane" id="lainnya" role="tabpanel">
        <div class="mb-3">
            <label for="jenis_tinggal_id" class="form-label">Jenis Tinggal</label>
            <select {{ $disabled ? 'disabled' : '' }}
                class="form-select @error('jenis_tinggal_id') is-invalid @enderror" name="jenis_tinggal_id"
                id="jenis_tinggal_id">
                <option value="">Pilih Jenis Tinggal</option>
                @foreach ($jenis_tinggal as $jenis)
                    <option value="{{ $jenis->id }}"
                        {{ isset($data) ? ($data->mahasiswa->jenis_tinggal_id == $jenis->id ? 'selected' : '') : (old('jenis_tinggal_id') == $jenis->id ? 'selected' : '') }}>
                        {{ $jenis->nama }}</option>
                @endforeach
            </select>
            @error('jenis_tinggal_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="alat_transportasi_id" class="form-label">Alat Transportasi</label>
            <select {{ $disabled ? 'disabled' : '' }}
                class="form-select @error('alat_transportasi_id') is-invalid @enderror" name="alat_transportasi_id"
                id="alat_transportasi_id">
                <option value="">Pilih Alat Transportasi</option>
                @foreach ($alat_transportasi as $alat)
                    <option value="{{ $alat->id }}"
                        {{ isset($data) ? ($data->mahasiswa->alat_transportasi_id == $alat->id ? 'selected' : '') : (old('alat_transportasi_id') == $alat->id ? 'selected' : '') }}>
                        {{ $alat->nama }}</option>
                @endforeach
            </select>
            @error('alat_transportasi_id')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

@if (!$disabled)
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
@endif

<script>
    function getRombel() {
        $('#rombel_id').empty().append(`<option value="">Pilih Rombel</option>`);
        return $.ajax({
            type: "GET",
            url: "{{ route('data-master.rombel.getDosenPa') }}",
            data: {
                prodi_id: $('#prodi_id').val(),
                tahun_ajaran_id: $('#tahun_masuk_id').val(),
                jenis_kelas_id: $('#jenis_kelas_id').val()
            },
            success: function(res) {
                $.each(res.data, function(i, e) {
                    $('#rombel_id').append(
                        `<option value="${e.id}">${e.nama} (${e.dosen_pa}|${e.nip_pa})</option>`
                    );
                })
            },
            error: function(err) {
                console.error('Gagal get rombel')
            }
        })
    }

    function getSemester() {
        $('#semester_id').empty().append(`<option value="">Pilih Semester</option>`);
        if ($('#tahun_masuk_id').val()) {
            return $.ajax({
                type: "GET",
                url: "{{ route('data-master.semester.get', ':tahun_ajaran_id') }}".replace(':tahun_ajaran_id',
                    $('#tahun_masuk_id').val()),
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#semester_id').append(
                            `<option value="${e.id}">${e.nama}</option>`
                        );
                    })
                },
                error: function(err) {
                    console.error('Gagal get semester')
                }
            })
        }
    }
    
    $('#tahun_masuk_id').on('change', getSemester);
    $('#tahun_masuk_id, #prodi_id, #jenis_kelas_id').on('change', getRombel)
</script>

@if (isset($data) || old('tahun_masuk_id') || old('prodi_id'))
    <script>
        getRombel().done(() => {
            $('#rombel_id').val('{{ isset($data) ? $data->mahasiswa->rombel_id : old('rombel_id') }}').trigger(
                'change')
        })

        getSemester().done(() => {
            $('#semester_id').val('{{ isset($data) ? $data->mahasiswa->semester_id : old('semester_id') }}').trigger(
                'change')
        })
    </script>
@endif
