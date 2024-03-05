@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Presensi {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-presensi.store', request('tahun_ajaran_id')) }}', 'Tambah Jadwal', '#jadwal', addJadwal)">
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-presensi">
                        <thead>
                            <tr>
                                <th>Kode Presensi</th>
                                <th>Materi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" method="get">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="jadwalLabel">Tambah Jadwal</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="div-alert"></div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Presensi</label>
                            <div class="d-flex" style="gap: 1rem;">
                                <input class="form-control" type="text" id="kode" name="kode" />
                                <button class="btn btn-primary btn-generate" onclick="generateCode()"
                                    type="button">Generate</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                            <select name="tahun_matkul_id" id="tahun_matkul_id" class="form-select" onchange="getTotal()">
                                <option value="">Pilih Pelajaran</option>
                                @foreach ($tahunMatkul as $matkul)
                                    <option value="{{ $matkul->id }}">{{ $matkul->nama }} | {{ $matkul->rombel }}
                                        |
                                        {{ config('services.hari')[$matkul->hari] }}, {{ $matkul->jam_mulai }} -
                                        {{ $matkul->jam_akhir }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="materi" class="form-label">Materi</label>
                            <input class="form-control" type="text" id="materi" name="materi" />
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea name="ket" id="ket" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Mulai
                            Pelajaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailJadwal" tabindex="-1" aria-labelledby="detailJadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" method="get">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="detailJadwalLabel">Tambah Jadwal</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="tab-main">
                            <ul class="nav nav-tabs">
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link active a-tab" href="#materi">Materi</a>
                                </li>
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link a-tab" href="#presensi">Presensi</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content py-4 px-1">
                            <div class="tab-pane active" id="materi" role="tabpanel">
                                <div class="div-alert"></div>
                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode Presensi</label>
                                    <div class="d-flex" style="gap: 1rem;">
                                        <input class="form-control" type="text" name="kode" readonly />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                                    <select class="form-select" disabled name="tahun_matkul_id">
                                        <option value="">Pilih Pelajaran</option>
                                        @foreach ($tahunMatkul as $matkul)
                                            <option value="{{ $matkul->id }}">{{ $matkul->nama }} |
                                                {{ $matkul->rombel }}
                                                |
                                                {{ config('services.hari')[$matkul->hari] }}, {{ $matkul->jam_mulai }} -
                                                {{ $matkul->jam_akhir }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="materi" class="form-label">Materi</label>
                                    <input class="form-control" type="text"readonly name="materi" />
                                </div>
                                <div class="mb-3">
                                    <label for="ket" class="form-label">Keterangan</label>
                                    <textarea cols="30" rows="10" class="form-control" name="ket" readonly></textarea>
                                </div>
                            </div>
                            <div class="tab-pane" id="presensi" role="tabpanel">
                                <div id="container-pie"></div>
                                <table class="table table-presensi-mahasiswa mt-3" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const optionsAbsensi = {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Presensi Kelas'
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Total',
                data: []
            }]
        };
    </script>
    <script>
        $('#kode').on('keyup', function() {
            const inputText = $(this).val();
            if (inputText.length > 6) {
                $(this).val(inputText.substring(0, 6));
            }
        })

        function generateCode() {
            $('#kode').val(generateRandomCode(6));
        }

        function addJadwal() {
            $('#jadwal .btn-generate, #jadwal #kode, #jadwal #tahun_matkul_id').removeAttr('disabled');
        }

        function editJadwal() {
            $('#jadwal .btn-generate, #jadwal #kode, #jadwal #tahun_matkul_id').attr('disabled', 'disabled');
        }

        let tablePresensi;
        let tablePresensiMahasiswa;
        let urlPresensiMahasiswa =
            '{{ route('kelola-presensi.dataPresensi', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'jadwal_id' => ':jadwal_id']) }}';
        $(document).ready(function() {
            tablePresensi = $('.table-presensi').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('kelola-presensi.data', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                columns: [{
                        "data": "kode"
                    },
                    {
                        "data": "materi"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            tablePresensiMahasiswa = $('.table-presensi-mahasiswa').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: urlPresensiMahasiswa,
                columns: [{
                        "data": "login_key"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "rombel"
                    }
                ],
                pageLength: 25,
            });
        });

        function chartPresensi(data) {
            $.ajax({
                url: "{{ route('kelola-presensi.dataChart', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'jadwal_id' => ':jadwal_id']) }}"
                    .replace(':jadwal_id', data.id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    optionsAbsensi.series[0].data = res.data;
                    Highcharts.chart('container-pie', optionsAbsensi);
                },
                error: function(err) {
                    alert('Gagal get chart');
                }
            })
        }

        function detailJadwal(data) {
            urlPresensiMahasiswa = urlPresensiMahasiswa.replace(':jadwal_id', data.id);
            tablePresensiMahasiswa.ajax.url(urlPresensiMahasiswa);
            tablePresensiMahasiswa.ajax.reload();

            chartPresensi(data)
        }

        function getTotal() {
            $('.div-alert').empty();

            let id = $('#tahun_matkul_id').val();
            if (id) {
                $.ajax({
                    url: "{{ route('kelola-presensi.getTotalPelajaran', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.total < 14) {
                            $('.div-alert').append(
                                `<div class="alert alert-primary" role="alert">Sudah terjadi ${res.total} kali pelajaran</div>`
                            );
                        } else {
                            $('.div-alert').append(
                                `<div class="alert alert-danger" role="alert">Sudah terjadi ${res.total} kali pelajaran. Tidak bisa melakukan pelajaran</div>`
                            )
                        }
                    },
                    error: function(err) {
                        showAlert(err.responseJSON.message)
                    }
                })
            }
        }
    </script>
@endpush
