@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Mata Kuliah</h5>
                    @can('add_matkul')
                        <div class="d-flex justify-content-center align-items-center" style="gap: 1rem">
                            {{-- <button type="button" class="btn btn-primary"
                                onclick="addForm('{{ route('data-master.mata-kuliah.store') }}', 'Tambah', '#matkul')">
                                Tambah
                            </button> --}}
                            <button class="btn btn-primary" onclick="getData()">Get NEO Feeder</button>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode MK</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="matkul" tabindex="-1" aria-labelledby="matkulLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="matkulLabel">Tambah Matkul</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select class="form-select" name="prodi_id" id="prodi_id" style="width: 100%" disabled>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input class="form-control" type="text" id="kode" name="kode" disabled/>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" disabled/>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_matkul" class="form-label">Jenis Matkul</label>
                            <select class="form-select" name="jenis_matkul" id="jenis_matkul" disabled>
                                <option value="">Pilih Jenis Matkul</option>
                                @foreach (config('services.matkul.jenis') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kel_matkul" class="form-label">Kelompok Matkul</label>
                            <select class="form-select" name="kel_matkul" id="kel_matkul" disabled>
                                <option value="">Pilih Kelompok Matkul</option>
                                @foreach (config('services.matkul.kelompok') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sks_mata_kuliah" class="form-label">SKS Mata Kuliah</label>
                            <input class="form-control" type="number" id="sks_mata_kuliah" name="sks_mata_kuliah"
                                value="0" min="0" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="sks_tatap_muka" class="form-label">SKS Tatap Muka</label>
                            <input class="form-control" type="number" id="sks_tatap_muka" name="sks_tatap_muka"
                                value="0" min="0" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="sks_praktek" class="form-label">SKS Praktek</label>
                            <input class="form-control" type="number" id="sks_praktek" name="sks_praktek" value="0"
                                min="0" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="sks_praktek_lapangan" class="form-label">SKS Praktek Lapangan</label>
                            <input class="form-control" type="number" id="sks_praktek_lapangan"
                                name="sks_praktek_lapangan" value="0" min="0" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="sks_simulasi" class="form-label">SKS Simulasi</label>
                            <input class="form-control" type="number" id="sks_simulasi" name="sks_simulasi"
                                value="0" min="0" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="ada_sap" class="form-label">ada SAP?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_sap" value="1"
                                        id="ada_sap_1" disabled>
                                    <label class="form-check-label" for="ada_sap_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_sap" value="0"
                                        id="ada_sap_0" checked disabled>
                                    <label class="form-check-label" for="ada_sap_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_silabus" class="form-label">ada Silabus?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_silabus" value="1"
                                        id="ada_silabus_1" disabled>
                                    <label class="form-check-label" for="ada_silabus_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_silabus" value="0"
                                        id="ada_silabus_0" checked disabled>
                                    <label class="form-check-label" for="ada_silabus_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_bahan_ajar" class="form-label">ada bahan ajar?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_bahan_ajar" value="1"
                                        id="ada_bahan_ajar_1" disabled>
                                    <label class="form-check-label" for="ada_bahan_ajar_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_bahan_ajar" value="0"
                                        id="ada_bahan_ajar_0" checked disabled>
                                    <label class="form-check-label" for="ada_bahan_ajar_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_acara_praktek" class="form-label">ada acara praktek?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_acara_praktek"
                                        value="1" id="ada_acara_praktek_1" disabled>
                                    <label class="form-check-label" for="ada_acara_praktek_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_acara_praktek"
                                        value="0" id="ada_acara_praktek_0" checked disabled>
                                    <label class="form-check-label" for="ada_acara_praktek_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_diklat" class="form-label">ada acara diklat?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_diklat" value="1"
                                        id="ada_diklat_1" disabled>
                                    <label class="form-check-label" for="ada_diklat_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_diklat" value="0"
                                        id="ada_diklat_0" checked disabled>
                                    <label class="form-check-label" for="ada_diklat_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_mulai_aktif" class="form-label">Tanggal mulai aktif</label>
                            <input class="form-control" type="date" id="tgl_mulai_aktif" name="tgl_mulai_aktif" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_akhir_aktif" class="form-label">Tanggal akhir aktif</label>
                            <input class="form-control" type="date" id="tgl_akhir_aktif" name="tgl_akhir_aktif" disabled />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.mata-kuliah.dataMatkul') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
    @if (Auth::user()->hasRole('admin'))
        <script>
            let thisPage = 'neo_feeder'
        </script>
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => 'matkul',
            'urlStoreData' => route('data-master.mata-kuliah.storeNeoFeeder'),
        ])
    @endif
@endpush
