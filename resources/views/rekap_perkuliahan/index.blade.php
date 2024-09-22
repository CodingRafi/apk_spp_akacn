@extends('mylayouts.main')

@section('container')
    <style>
        .select2-container {
            z-index: 999;
        }
    </style>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Rekap Kelas Perkuliahan</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rekapPerkuliahanModal">
                            Get Neo Feeder
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-3 mb-3">
                        <select id="filter-semester">
                            <option value="">Pilih Semester</option>
                            @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-matkul" aria-label="Data matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode MK</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rekapPerkuliahanModal" tabindex="-1" aria-labelledby="rekapPerkuliahanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rekapPerkuliahanLabel">Get Neo Feeder</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="semester-id-modal" class="semester-id-modal" style="width: 100%">
                        <option value="">Pilih Semester</option>
                        @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="getDataNeoFeeder()">Get</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table
        $(document).ready(function() {
            table = $('.table-matkul').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('rekap-perkuliahan.data') }}',
                    data: function(p) {
                        p.semester_id = $('#filter-semester').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "options"
                    }
                ]
            });
            $("#filter-semester").select2();

            $(".semester-id-modal").select2({
                dropdownParent: $('#rekapPerkuliahanModal')
            });
        });

        $('#filter-semester').on('change', function() {
            table.ajax.reload();
        });

    </script>
    @include('neo_feeder')
    <script>
        let kelasKuliah = [];
        let dosenKelasKuliah = [];
        let statusGetKelasKuliah = false;
        let statusGetDosenKelasKuliah = false;
        let semesterId = null;

        async function getDataNeoFeeder() {
            if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
                kelasKuliah = [];
                dosenKelasKuliah = [];
                statusGetKelasKuliah = false;
                statusGetDosenKelasKuliah = false;
                semesterId = $('#semester-id-modal').val();

                $.LoadingOverlay("show");
                if (!url) {
                    showAlert('Url tidak ditemukan', 'error');
                }

                token = await getToken();

                if (token === null) {
                    showAlert('GAGAL GET TOKEN', 'error');
                    $.LoadingOverlay("hide");
                    return false;
                }
                getKelasKuliahNeoFeeder(token);
                getDosenKelasKuliah(token);
            }
        }

        async function getKelasKuliahNeoFeeder(token) {
            let raw = {
                "act": "GetDetailKelasKuliah",
                "filter": `id_semester='${semesterId}'`,
                "order": "",
                "token": token.data.token
            };

            const limit = 100;
            let loop = 0;
            let keepRunning = true;

            try {
                while (keepRunning) {
                    showAlert(`Loop ${loop + 1} kelas kuliah sedang berjalan`, 'success');

                    raw.limit = limit;
                    raw.offset = loop * limit;

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const response = await $.ajax(settings);
                    if (response.data.length > 0) {
                        kelasKuliah = kelasKuliah.concat(response.data);
                    } else {
                        keepRunning = false;
                    }
                    
                    loop++;
                }
                
                for (let i = 0; i < kelasKuliah.length; i++) {
                    const mhs = await getMhsKelasKuliah(token, kelasKuliah[i].id_kelas_kuliah);
                    kelasKuliah[i].mahasiswa = mhs;
                }
                
                statusGetKelasKuliah = true;
                storeDataNeoFeeder()

                $.LoadingOverlay("hide");
            } catch (error) {
                $.LoadingOverlay("hide");
                console.error("AJAX Error:", error);
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }

        async function getMhsKelasKuliah(token, id_kelas_kuliah) {
            let raw = {
                "act": "GetPesertaKelasKuliah",
                "filter": `id_kelas_kuliah='${id_kelas_kuliah}'`,
                "order": "",
                "token": token.data.token
            };

            const limit = 100;
            let loop = 0;
            let keepRunning = true;
            let mhsKelasKuliah = [];

            try {
                while (keepRunning) {
                    showAlert(`Loop ${loop + 1} mahasiswa sedang berjalan`, 'success');

                    raw.limit = limit;
                    raw.offset = loop * limit;

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const response = await $.ajax(settings);
                    if (response.data.length > 0) {
                        mhsKelasKuliah = mhsKelasKuliah.concat(response.data);
                    } else {
                        keepRunning = false;
                    }

                    loop++;
                }

                $.LoadingOverlay("hide");
            } catch (error) {
                $.LoadingOverlay("hide");
                console.error("AJAX Error:", error);
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }

            return mhsKelasKuliah
        }

        async function getDosenKelasKuliah(token) {
            let raw = {
                "act": "GetDosenPengajarKelasKuliah",
                "filter": `id_semester='${semesterId}'`,
                "order": "",
                "token": token.data.token
            };

            const limit = 100;
            let loop = 0;
            let keepRunning = true;

            try {
                while (keepRunning) {
                    showAlert(`Loop ${loop + 1} dosen sedang berjalan`, 'success');

                    raw.limit = limit;
                    raw.offset = loop * limit;

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const response = await $.ajax(settings);
                    if (response.data.length > 0) {
                        dosenKelasKuliah = dosenKelasKuliah.concat(response.data);
                    } else {
                        statusGetDosenKelasKuliah = true;
                        keepRunning = false;
                        storeDataNeoFeeder()
                    }

                    loop++;
                }

                $.LoadingOverlay("hide");
            } catch (error) {
                $.LoadingOverlay("hide");
                console.error("AJAX Error:", error);
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }

        async function ajaxSend(chunk) {
            return $.ajax({
                url: '{{ route('rekap-perkuliahan.storeNeoFeeder') }}',
                type: 'POST',
                data: {
                    data: JSON.stringify(chunk),
                    semester_id: semesterId
                },
                dataType: 'json'
            })
        }

        async function storeDataNeoFeeder() {
            if (statusGetKelasKuliah && statusGetDosenKelasKuliah) {
                const data = parseData();
                console.log(data)
                const chunks = chunkArray(data, 20)

                for (let i = 0; i < chunks.length; i++) {
                    try {
                        await ajaxSend(chunks[i]);
                        showAlert('Data Berhasil dikirim', 'success');
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan saat mengirim data', 'error');
                    }
                }
            }
        }

        function parseData() {
            let data = [];

            kelasKuliah.forEach(row => {
                let rowParse = {
                    id_kelas_kuliah: row.id_kelas_kuliah,
                    id_matkul: row.id_matkul,
                    id_prodi: row.id_prodi,
                    id_semester: row.id_semester,
                    nama: row.nama_kelas_kuliah,
                    bahasan: row.bahasan,
                    tanggal_mulai_efektif: row.tanggal_mulai_efektif,
                    tanggal_akhir_efektif: row.tanggal_akhir_efektif,
                    mahasiswa: row.mahasiswa
                }

                let dosen = dosenKelasKuliah.filter(function(item) {
                    return item.id_kelas_kuliah == row.id_kelas_kuliah;
                }).map(function(item) {
                    return {
                        id_aktivitas_mengajar: item.id_aktivitas_mengajar,
                        id_dosen: item.id_dosen,
                        id_jenis_evaluasi: item.id_jenis_evaluasi,
                        id_registrasi_dosen: item.id_registrasi_dosen,
                        sks_substansi_total: item.sks_substansi_total,
                        rencana_tatap_muka: item.rencana_minggu_pertemuan,
                        realisasi_tatap_muka: item.realisasi_minggu_pertemuan,
                        jenis_evaluasi_id: item.id_jenis_evaluasi
                    };
                })

                rowParse.dosen = dosen;
                data.push(rowParse);
            })

            return data;
        }
    </script>
@endpush
