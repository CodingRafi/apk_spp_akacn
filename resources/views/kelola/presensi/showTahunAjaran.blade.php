@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Presensi {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('kelola-presensi.presensi.store', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Jadwal', '#jadwal')">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <select id="filter-prodi" class="form-select" onchange="getSemester();getMatkul();">
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
                            <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                            <select name="tahun_matkul_id" id="tahun_matkul_id" class="form-select" onchange="get_materi()">
                                <option value="">Pilih Pelajaran</option>
                                @foreach ($tahunMatkul as $matkul)
                                    <option value="{{ $matkul->id }}">{{ $matkul->nama }}
                                        |
                                        {{ config('services.hari')[$matkul->hari] }}, {{ $matkul->jam_mulai }} -
                                        {{ $matkul->jam_akhir }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="materi_id" class="form-label">Materi</label>
                            <select name="materi_id" id="materi_id" class="form-control">
                                <option value="">Pilih Materi</option>
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
@endsection

@push('js')
    <script>
        function generateCode() {
            $('#kode').val(generateRandomCode(6));
        }

        function getTotal() {
            $('.div-alert').empty();

            let id = $('#tahun_matkul_id').val();
            if (id) {
                $.ajax({
                    url: "{{ route('kelola-presensi.presensi.getTotalPelajaran', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
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

        function get_materi() {
            $('#materi_id').empty().append(`<option value="">Pilih Materi</option>`);

            if ($('#tahun_matkul_id').val()) {
                $.ajax({
                    url: "{{ route('kelola-presensi.presensi.getMateri', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', $('#tahun_matkul_id').val()),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#materi_id').append(`<option value="${e.id}">${e.materi}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get materi');
                    }
                })
            }
        }

        function getSemester() {
            let prodi_id = $('#filter-prodi').val();
            $('#filter-tahun-semester').empty().append(`<option value="">Pilih Semester</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.presensi.getSemester', ":prodi_id") }}'.replace(':prodi_id',
                    prodi_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#filter-tahun-semester').append(`<option value="${e.id}">${e.nama}</option>`)
                    })
                },
                error: function(err) {
                    alert('Gagal get semester');
                }
            })
        }

        function getMatkul() {
            let prodi_id = $('#filter-prodi').val();
            $('#filter-tahun-matkul').empty().append(`<option value="">Pilih Matkul</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.presensi.getMatkul', ['prodi_id' => ":prodi_id"]) }}'
                    .replace(':prodi_id', prodi_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#filter-tahun-matkul').append(`<option value="${e.id}">${e.nama}</option>`)
                    })
                },
                error: function(err) {
                    alert('Gagal get matkul');
                }
            })
        }

        function getPengajar(tahun_matkul_id) {
            $.ajax({
                url: '{{ route('kelola-presensi.presensi.getPengajar', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                    .replace(':tahun_matkul_id', tahun_matkul_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#pengajar_id').append(
                            `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                    })
                },
                error: function(err) {
                    alert('Gagal get pengajar');
                }
            })
        }

        function getPengawas() {
            $.ajax({
                url: '{{ route('kelola-presensi.presensi.getPengawas') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#pengajar_id').append(
                            `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                    })
                },
                error: function(err) {
                    alert('Gagal get pengawas');
                }
            })
        }

        function generateForm() {
            if ($('#type').val()) {
                $('.div-ujian, .div-pengajar').empty();
                $('.div-pengajar').html($('#select-pengajar').html());
                $('#pengajar_id').empty();
                if ($('#type').val() == 'ujian') {
                    $('label[for="pengajar_id"]').text('Pengawas');
                    $('#pengajar_id').append('<option value="">Pilih Pengawas</option>');
                    $('.div-ujian').html($('#select-ujian').html());
                    getPengawas();
                    getJenisUjian();
                } else {
                    $('label[for="pengajar_id"]').text('Asdos');
                    $('#pengajar_id').append('<option value="">Pilih Asdos</option>');
                    if ($('#tahun_matkul_id').val()) {
                        getTotal();
                        getPengajar($('#tahun_matkul_id').val());
                    }
                }
            }
        }

        function getJenisUjian(tahun_matkul_id) {
            $.ajax({
                url: '{{ route('kelola-presensi.presensi.getJenisUjian', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                    .replace(':tahun_matkul_id', tahun_matkul_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#jenis').empty().append(`<option value="">Pilih Jenis Ujian</option>`);
                    $.each(res.data, function(i, e) {
                        $('#jenis').append(`<option value="${e}">${e.toUpperCase()}</option>`)
                    })
                },
                error: function(err) {
                    alert('Gagal get jenis ujian');
                }
            })
        }

        $('#type, #tahun_matkul_id').on('change', generateForm);

        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.presensi.getJadwal', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                    data: function(p) {
                        p.prodi_id = $('#filter-prodi').val();
                        p.tahun_semester_id = $('#filter-tahun-semester').val();
                        p.tahun_matkul_id = $('#filter-tahun-matkul').val();
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
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });

        $('#filter-tahun-semester, #filter-tahun-matkul, #filter-prodi').on('change', function() {
            table.ajax.reload();
        })
    </script>
@endpush
