@extends('mylayouts.main')

@push('css')
    <style>
        .select2-container:has(span[aria-labelledby="select2-filter-tahun-ajaran-container"]) {
            z-index: 999;
        }
    </style>
@endpush

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card" id="card-list-jadwal">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize mb-0">Jadwal</h5>
                    @can('add_kelola_presensi')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-presensi.jadwal.store') }}', 'Tambah Jadwal', '#jadwal', clearForm)">
                            Tambah
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-ajaran" class="select2"
                                onchange="getSemester();filterGetMatkul();" style="width: 100%;">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-prodi" class="form-select" onchange="getSemester();filterGetMatkul();">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-semester" class="form-select">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-matkul" class="form-select">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-status" class="form-select">
                                <option value="all">Pilih Status</option>
                                <option value="1">Menunggu Verifikasi</option>
                                <option value="2">Disetujui</option>
                                <option value="3">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Presensi</th>
                                    <th>Kode Matkul</th>
                                    <th>Tanggal</th>
                                    <th>Matkul</th>
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
    <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('kelola-presensi.whitelist-ip.store') }}" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="jadwalLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="div-alert"></div>
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select name="prodi_id" id="prodi_id" class="form-select" style="width: 100%" onchange="getPelajaran()">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" id="tahun_ajaran_id" style="width: 100%" onchange="getPelajaran()">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                            <select name="tahun_matkul_id" id="tahun_matkul_id" style="width: 100%">
                                <option value="">Pilih Pelajaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Presensi</label>
                            <div class="d-flex" style="gap: 1rem;">
                                <input class="form-control" type="text" id="kode" name="kode" />
                                <button class="btn btn-primary btn-generate" onclick="generateCode()"
                                    type="button">Generate</button>
                            </div>
                        </div>
                        @if (getRole()->name == 'admin')
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Pilih Type</option>
                                    <option value="ujian">Ujian</option>
                                    <option value="pertemuan">Pertemuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tgl" class="form-label">Tanggal</label>
                                <input class="form-control" type="date" name="tgl" />
                            </div>
                            <div class="div-ujian"></div>
                            <div class="div-pengajar"></div>
                        @else
                            <input type="hidden" name="type" value="pertemuan">
                            <input type="hidden" name="pengajar_id" value="{{ Auth::user()->id }}">
                            <div class="mb-3">
                                <label for="materi_id" class="form-label">Materi</label>
                                <select name="materi_id" id="materi_id" class="form-control">
                                    <option value="">Pilih Materi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                <textarea cols="30" rows="10" class="form-control" name="ket"></textarea>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => table.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="select-ujian">
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Ujian</label>
            <select name="jenis" id="jenis" class="form-control">
                <option value="">Pilih Jenis Ujian</option>
            </select>
        </div>
    </template>

    <template id="select-pengajar">
        <div class="mb-3">
            <label for="pengajar_id" class="form-label"></label>
            <select name="pengajar_id" id="pengajar_id" class="form-control">
                <option value="">Pilih</option>
            </select>
        </div>
    </template>

    <template id="select-materi">
        <div class="mb-3">
            <label for="materi_id" class="form-label">Materi</label>
            <select name="materi_id" id="materi_id" class="form-control">
                <option value="">Pilih Materi</option>
            </select>
        </div>
    </template>
@endsection

@push('js')
    <script>
        function generateCode() {
            $('#kode').val(generateRandomCode(6));
        }

        function getTotal() {
            $('.div-alert').empty();
            const tahun_ajaran_id = $('#tahun_ajaran_id').val();
            const tahun_matkul_id = $('#tahun_matkul_id').val();
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.getTotalPelajaran', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $('.div-alert').empty();
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

        $('#type, #tahun_matkul_id').on('change', get_materi);

        function get_materi(data = {}) {
            $('#materi_id').empty().append(`<option value="">Pilih Materi</option>`);
            const tahun_matkul_id = data.tahun_matkul_id ?? $('#tahun_matkul_id').val();
            const tahun_ajaran_id = data.tahun_ajaran_id ?? $('#tahun_ajaran_id').val();

            @if (getRole()->name == 'admin')
                const check = tahun_matkul_id && tahun_ajaran_id && $('#type').val() && $('#type')
                .val() == 'pertemuan';
            @else
                const check = tahun_matkul_id && tahun_ajaran_id;
            @endif

            console.log(tahun_matkul_id)
            console.log(tahun_ajaran_id)

            if (check) {
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.getMateri', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        except: data.materi_id
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#materi_id').append(
                                `<option value="${e.id}">${e.materi} (${e.type})</option>`)
                        })

                        if (data.materi_id) {
                            $('#materi_id').val(data.materi_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get materi');
                    }
                })
            }
        }

        function getSemester() {
            const prodi_id = $('#filter-prodi').val();
            const tahun_ajaran_id = $('#filter-tahun-ajaran').val();

            if (prodi_id && tahun_ajaran_id) {
                $('#filter-tahun-semester').empty().append(`<option value="">Pilih Semester</option>`);
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getSemester', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id',
                            prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-tahun-semester').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get semester');
                    }
                })
            }
        }

        function filterGetMatkul() {
            const prodi_id = $('#filter-prodi').val();
            const tahun_ajaran_id = $('#filter-tahun-ajaran').val();
            $('#filter-tahun-matkul').empty().append(`<option value="">Pilih Matkul</option>`);
            if (prodi_id && tahun_ajaran_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getMatkul', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id', prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-tahun-matkul').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }

        function getPelajaran() {
            const prodi_id = $('#prodi_id').val();
            const tahun_ajaran_id = $('#tahun_ajaran_id').val();
            if (prodi_id && tahun_ajaran_id) {
                $('#tahun_matkul_id').empty().append(`<option value="">Pilih Pelajaran</option>`);
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getPelajaran', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id', prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#tahun_matkul_id').append(
                                `<option value="${e.id}">${e.label}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }

        function getPengajar(tahun_ajaran_id, tahun_matkul_id, data = {}) {
            $('#pengajar_id').empty().append(`<option value="">Pilih Pengajar</option>`);
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getPengajar', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#pengajar_id').append(
                                `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                        })

                        if (data.pengajar_id) {
                            console.log(res.data)
                            $('#pengajar_id').val(data.pengajar_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get pengajar');
                    }
                })
            }
        }

        function getPengawas(data = {}) {
            $('#pengajar_id').empty().append(`<option value="">Pilih Pengawas</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.getPengawas') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#pengajar_id').append(
                            `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                    })

                    if (data.pengajar_id) {
                        console.log(data.pengajar_id)
                        $('#pengajar_id').val(data.pengajar_id);
                    }
                },
                error: function(err) {
                    alert('Gagal get pengawas');
                }
            })
        }

        function clearForm() {
            $(`#tahun_ajaran_id, #tahun_matkul_id`).trigger("change");
            $('.div-ujian, .div-pengajar, .div-alert').empty();
        }

        // $('#type, #tahun_matkul_id').on('change', () => {
        //     if ($('#type').val() || $('#tahun_matkul_id').val()) {
        //         clearForm();
        //     }
        // });

        function editJadwal(data) {
            clearForm();
            $(`#tahun_ajaran_id, #tahun_matkul_id`).trigger("change");
            generateForm(data)
        }

        function generateForm(data = {}) {
            let type = data.type ?? $('#type').val();
            let tahun_matkul_id = data.tahun_matkul_id ?? $('#tahun_matkul_id').val();
            let tahun_ajaran_id = data.tahun_ajaran_id ?? $('#tahun_ajaran_id').val();

            if (type && tahun_matkul_id && tahun_ajaran_id) {
                $('.div-ujian, .div-pengajar, .div-alert').empty();
                $('.div-pengajar').html($('#select-pengajar').html());
                $('#pengajar_id').empty();
                if (type == 'ujian') {
                    $('label[for="pengajar_id"]').text('Pengawas');
                    $('#pengajar_id').append('<option value="">Pilih Pengawas</option>');
                    $('.div-ujian').html($('#select-ujian').html());
                    getPengawas(data);
                    getJenisUjian(tahun_ajaran_id, tahun_matkul_id, data);
                } else {
                    $('label[for="pengajar_id"]').text('Pengajar');
                    $('#pengajar_id').append('<option value="">Pilih Pengajar</option>');
                    $('.div-pengajar').append($('#select-materi').html());
                    console.log(data)
                    if (data.materi_id) {
                        get_materi(data);
                    }

                    if (tahun_ajaran_id && tahun_matkul_id) {
                        getTotal();
                        getPengajar(tahun_ajaran_id, tahun_matkul_id, data);
                    }
                }
            }
        }

        function getJenisUjian(tahun_ajaran_id, tahun_matkul_id, data = {}) {
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getJenisUjian', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        except: data.jenis_ujian
                    },
                    success: function(res) {
                        $('#jenis').empty().append(`<option value="">Pilih Jenis Ujian</option>`);
                        $.each(res.data, function(i, e) {
                            $('#jenis').append(`<option value="${e}">${e.toUpperCase()}</option>`)
                        })

                        if (data.jenis_ujian) {
                            $('#jenis').val(data.jenis_ujian);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get jenis ujian');
                    }
                })
            }
        }

        $('#type, #tahun_matkul_id').on('change', () => generateForm());

        let table;
        $(document).ready(function() {
            $('#tahun_ajaran_id, #tahun_matkul_id').select2({
                dropdownParent: $("#jadwal")
            })

            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.jadwal.data') }}',
                    data: function(p) {
                        p.prodi_id = $('#filter-prodi').val();
                        p.tahun_semester_id = $('#filter-tahun-semester').val();
                        p.tahun_matkul_id = $('#filter-tahun-matkul').val();
                        p.tahun_ajaran_id = $('#filter-tahun-ajaran').val();
                        p.status = $('#filter-status').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "kode_matkul"
                    },
                    {
                        "data": "tgl"
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
                ],
                pageLength: 25,
            });
        });

        $('#filter-tahun-semester, #filter-tahun-matkul, #filter-prodi, #filter-status, #filter-tahun-ajaran').on('change', function() {
            table.ajax.reload();
        })
    </script>
@endpush
