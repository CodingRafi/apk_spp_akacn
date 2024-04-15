@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Rekap Kelas Perkuliahan</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="sendDataToNeoFeeder('{{ $tahunMatkul->id_kelas_kuliah }}')">Kirim ke Neo
                        Feeder</button>
                </div>
                <div class="card-body">
                    @if ($tahunMatkul->id_kelas_kuliah)
                        <div class="alert alert-success">Sudah dikirim ke neo feeder</div>
                    @else
                        <div class="alert alert-warning">Belum dikirim ke neo feeder</div>
                    @endif
                    <form
                        action="{{ route('data-master.tahun-ajaran.matkul.rekap.update', ['id' => request('id'), 'matkul_id' => request('matkul_id'), 'tahun_semester_id' => request('tahun_semester_id')]) }}"
                        method="post" class="mb-3">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prodi_id" class="form-label">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-control" disabled>
                                        <option value="">Pilih Program Studi</option>
                                        @foreach ($prodis as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $tahunMatkul->prodi_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="matkul" class="form-label">Mata Kuliah</label>
                                    <input type="text" id="matkul" class="form-control"
                                        value="{{ $tahunMatkul->kode }} - {{ $tahunMatkul->matkul }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="sks_mata_kuliah" class="form-label">Bobot Mata Kuliah</label>
                                    <input type="text" id="sks_mata_kuliah" class="form-control"
                                        value="{{ $tahunMatkul->sks_mata_kuliah }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="sks_praktek" class="form-label">Bobot Praktikum</label>
                                    <input type="text" id="sks_praktek" class="form-control"
                                        value="{{ $tahunMatkul->sks_praktek }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="bahasan" class="form-label">Bahasan</label>
                                    <input type="text" id="bahasan" class="form-control" name="bahasan"
                                        value="{{ $tahunMatkul->bahasan }}">
                                </div>
                                <div class="mb-3">
                                    <label for="lingkup" class="form-label">Lingkup</label>
                                    <select class="form-select" name="lingkup" id="lingkup" disabled>
                                        <option value="">Pilih Lingkup</option>
                                        <option value="1" {{ $tahunMatkul->lingkup == 1 ? 'selected' : '' }}>Internal
                                        </option>
                                        <option value="2" {{ $tahunMatkul->lingkup == 2 ? 'selected' : '' }}>Eksternal
                                        </option>
                                        <option value="3" {{ $tahunMatkul->lingkup == 3 ? 'selected' : '' }}>Campuran
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_mulai_efektif" class="form-label">Tanggal Mulai Efektif</label>
                                    <input type="date" id="tanggal_mulai_efektif" class="form-control"
                                        name="tanggal_mulai_efektif" value="{{ $tahunMatkul->tanggal_mulai_efektif }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <input type="text" id="semester" class="form-control"
                                        value="{{ $semester->semester }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Kelas</label>
                                    <input type="text" id="semester"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ $tahunMatkul->nama }}" name="nama">
                                    @error('nama')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="sks_tatap_muka" class="form-label">Bobot Tatap Muka</label>
                                    <input type="text" id="sks_tatap_muka" class="form-control"
                                        value="{{ $tahunMatkul->sks_tatap_muka }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="sks_praktek_lapangan" class="form-label">Bobot Praktek Lapangan</label>
                                    <input type="text" id="sks_praktek_lapangan" class="form-control"
                                        value="{{ $tahunMatkul->sks_praktek_lapangan }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="sks_simulasi" class="form-label">Bobot Simulasi</label>
                                    <input type="text" id="sks_simulasi" class="form-control"
                                        value="{{ $tahunMatkul->sks_simulasi }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="mode" class="form-label">Mode</label>
                                    <select class="form-select" name="mode" id="mode" disabled>
                                        <option value="">Pilih Mode</option>
                                        <option value="F" {{ $tahunMatkul->mode == 'F' ? 'selected' : '' }}>Offline
                                        </option>
                                        <option value="O" {{ $tahunMatkul->mode == 'O' ? 'selected' : '' }}>Online
                                        </option>
                                        <option value="M" {{ $tahunMatkul->mode == 'M' ? 'selected' : '' }}>Campuran
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_akhir_efektif" class="form-label">Tanggal Akhir Efektif</label>
                                    <input type="date" id="tanggal_akhir_efektif" class="form-control"
                                        name="tanggal_akhir_efektif" value="{{ $tahunMatkul->tanggal_akhir_efektif }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                    <hr>
                    <ul class="nav nav-tabs" id="kelasKuliah">
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link active a-tab"
                                data-bs-target="#dosen">Dosen</button>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                                data-bs-target="#mahasiswa">Mahasiswa</button>
                        </li>
                    </ul>
                    <div class="tab-content py-4 px-1" id="profileContent">
                        <div class="tab-pane active" id="dosen" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-dosen" aria-label="table-dosen">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center">No</th>
                                            <th rowspan="2" class="text-center">NIDN</th>
                                            <th rowspan="2" class="text-center">Nama</th>
                                            <th rowspan="2" class="text-center">Bobot</th>
                                            <th colspan="2" class="text-center">Pertemuan</th>
                                            <th rowspan="2" class="text-center">Jenis Evaluasi</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Rencana</th>
                                            <th class="text-center">Realisasi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="mahasiswa" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-mhs w-100" aria-label="table-mhs">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">NIM</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Jenis Kelamin</th>
                                            <th class="text-center">Program Studi</th>
                                            <th class="text-center">Angkatan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('data_master.tahun_ajaran.matkul.neo_feeder')
    <script>
        $(document).ready(function() {
            $('.table-dosen').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data-master.tahun-ajaran.matkul.rekap.getDosen', ['id' => request('id'), 'matkul_id' => request('matkul_id'), 'tahun_semester_id' => request('tahun_semester_id')]) }}",
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "login_key"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "sks_substansi_total"
                    },
                    {
                        "data": "rencana_tatap_muka"
                    },
                    {
                        "data": "realisasi_tatap_muka"
                    },
                    {
                        "data": "jenisEvaluasi"
                    },
                ]
            });

            $('.table-mhs').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data-master.tahun-ajaran.matkul.rekap.getMhs', ['id' => request('id'), 'matkul_id' => request('matkul_id'), 'tahun_semester_id' => request('tahun_semester_id')]) }}",
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "login_key"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "jk"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "tahun_masuk_id"
                    },
                ]
            });
        });
    </script>
@endpush
