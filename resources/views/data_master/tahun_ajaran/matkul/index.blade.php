@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Mata Kuliah</h5>
                        @can('add_matkul')
                            <div class="d-flex" style="gap: 1rem;">
                                <button type="button" class="btn btn-primary" onclick="getDataNeoFeeder()">
                                    Get Neo Feeder
                                </button>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.tahun-ajaran.matkul.store', request('id')) }}', 'Tambah Mata Kuliah', '#Matkul')">
                                    Tambah
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-4">
                        <select id="filter-prodi" class="form-control mb-3">
                            <option value="">Pilih Prodi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Dosen</th>
                                    <th>Rombel</th>
                                    @can('edit_matkul', 'delete_matkul')
                                        <th>Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@can('add_matkul')
    <div class="modal fade" id="Matkul" tabindex="-1" role="dialog" aria-labelledby="MatkulLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-matkul">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="MatkulLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="div-alert"></div>
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select class="form-select" name="prodi_id" id="prodi_id"
                                onchange="get_matkul();get_rombel();">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="matkul_id" class="form-label">Mata Kuliah</label>
                            <select class="form-select select2-matkul" name="matkul_id" id="matkul_id" style="width: 100%">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ruang_id" class="form-label">Ruang</label>
                            <select class="form-select select2" name="ruang_id[]" id="ruang_id" multiple
                                style="width: 100%">
                                @foreach ($ruangs as $ruang)
                                    <option value="{{ $ruang->id }}">{{ $ruang->nama }} (Kapasitas:
                                        {{ $ruang->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select class="form-select" name="hari" id="hari">
                                <option value="">Pilih Hari</option>
                                @foreach (config('services.hari') as $key => $hari)
                                    <option value="{{ $key }}">{{ $hari }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <input class="form-control" type="time" id="jam_mulai" placeholder="Jam Mulai"
                                        name="jam_mulai" min="00:00" max="24:00" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_akhir" class="form-label">Jam Akhir</label>
                                    <input class="form-control" type="time" id="jam_akhir" placeholder="Jam akhir"
                                        name="jam_akhir" min="00:00" max="24:00" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rombel_id" class="form-label">Rombel</label>
                            <select class="form-select select2" name="rombel_id[]" id="rombel_id" style="width: 100%"
                                multiple>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mode" class="form-label">Mode</label>
                            <select class="form-select" name="mode" id="mode">

                                <option value="">Pilih Mode</option>
                                <option value="F">Offline</option>
                                <option value="O">Online</option>
                                <option value="M">Campuran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lingkup" class="form-label">Lingkup</label>
                            <select class="form-select" name="lingkup" id="lingkup">
                                <option value="">Pilih Lingkup</option>
                                <option value="1">Internal</option>
                                <option value="2">Eksternal</option>
                                <option value="3">Campuran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cek_ip" class="form-label">Cek IP?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="cek_ip"
                                    value="1" id="cek_ip">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableMatkul.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@push('js')
    @include('neo_feeder')
    <script>
        let kelasKuliah = [];
        let mhsKelasKuliah = [];
        let dosenKelasKuliah = [];
        let statusGetKelasKuliah = false;
        let statusGetMhsKelasKuliah = false;
        let statusGetDosenKelasKuliah = false;

        async function getDataNeoFeeder() {
            if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
                kelasKuliah = [];
                mhsKelasKuliah = [];
                dosenKelasKuliah = [];
                statusGetKelasKuliah = false;
                statusGetMhsKelasKuliah = false;
                statusGetDosenKelasKuliah = false;

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
                getMhsKelasKuliah(token);
                getDosenKelasKuliah(token);
            }
        }

        async function getKelasKuliahNeoFeeder(token) {
            let raw = {
                "act": "GetDetailKelasKuliah",
                "filter": "{!! $semester !!}",
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
                        statusGetKelasKuliah = true;
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

        async function getMhsKelasKuliah(token) {
            let raw = {
                "act": "GetPesertaKelasKuliah",
                "filter": "angkatan='{{ request('id') }}'",
                "order": "",
                "token": token.data.token
            };

            const limit = 100;
            let loop = 0;
            let keepRunning = true;

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
                        statusGetMhsKelasKuliah = true;
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

        async function getDosenKelasKuliah(token) {
            let raw = {
                "act": "GetDosenPengajarKelasKuliah",
                "filter": "{!! $semester !!}",
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
                url: '{{ route('data-master.tahun-ajaran.matkul.storeNeoFeeder', ['id' => request('id')]) }}',
                type: 'POST',
                data: {
                    data: JSON.stringify(chunk),
                },
                dataType: 'json'
            })
        }

        async function storeDataNeoFeeder() {
            if (statusGetKelasKuliah && statusGetMhsKelasKuliah && statusGetDosenKelasKuliah) {
                const data = parseData();
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
                }

                let mhs = mhsKelasKuliah.filter(function(item) {
                    return item.id_kelas_kuliah == row.id_kelas_kuliah;
                }).map(function(item) {
                    return {
                        id_mahasiswa: item.id_mahasiswa,
                        id_registrasi_mahasiswa: item.id_registrasi_mahasiswa,
                        angkatan: item.angkatan,
                    };
                })

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

                rowParse.mahasiswa = mhs;
                rowParse.dosen = dosen;
                data.push(rowParse);
            })

            return data;
        }
    </script>
    <script>
        function editTahunMatkul(data) {
            get_matkul(data);
            get_rombel(data);
        }

        function get_matkul(data = {}) {
            let id = $('#prodi_id').val();
            $('#matkul_id').empty().append(
                '<option value="">Pilih Mata Kuliah</option>'
            );
            $.ajax({
                url: "{{ route('data-master.tahun-ajaran.matkul.getMatkul', ['id' => request('id'), 'prodi_id' => ':id']) }}"
                    .replace(':id', id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(key, value) {
                        $('#matkul_id').append(`<option value="${value.id}">${value.kode} - ${value.nama}</option>`);
                    })

                    if (data.matkul_id) {
                        $('#matkul_id').val(data.matkul_id)
                    }
                },
                error: function(err) {
                    alert('Gagal get matkul')
                }
            })
        }

        function get_rombel(data = {}) {
            let id = $('#prodi_id').val();
            $('#rombel_id').empty();

            if (id && id !== '') {
                $.ajax({
                    url: "{{ route('data-master.tahun-ajaran.matkul.getRombel', ['id' => request('id'), 'prodi_id' => ':id']) }}"
                        .replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(key, value) {
                            $('#rombel_id').append(
                                `<option value="${value.id}">${value.nama}</option>`);
                        })

                        if (data.rombel_id) {
                            $('#rombel_id').val(data.rombel_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get rombel')
                    }
                })
            }
        }

        let tableMatkul;

        $(document).ready(function() {
            tableMatkul = $('.table-matkul').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.tahun-ajaran.matkul.data', request('id')) }}',
                    data: function(d) {
                        d.prodi_id = $('#filter-prodi').val();
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
                        "data": "dosen"
                    },
                    {
                        "data": "rombel"
                    },
                    @can('edit_matkul', 'delete_matkul')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
            });
        });

        $('#filter-prodi').on('change', function() {
            tableMatkul.ajax.reload();
        });

        $(document).ready(function(){
            $('.select2-matkul').select2({
                dropdownParent: $("#Matkul")
            })
        })
    </script>
@endpush
