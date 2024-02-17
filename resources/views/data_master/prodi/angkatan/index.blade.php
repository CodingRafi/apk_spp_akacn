@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('data-master.prodi.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Angkatan {{ request('tahun_ajaran_id') }}</h5>
                </div>
                <div class="card-body">
                    <div id="tab-main">
                        <ul class="nav nav-tabs">
                            <li class="nav-item" style="white-space: nowrap;">
                                <a class="nav-link a-tab active" href="#semester">Semester</a>
                            </li>
                            <li class="nav-item" style="white-space: nowrap;">
                                <a class="nav-link a-tab" href="#pembayaran-semester">Pembayaran Semester</a>
                            </li>
                            <li class="nav-item" style="white-space: nowrap;">
                                <a class="nav-link a-tab" href="#pembayaran-lainnya">Pembayaran Lainnya</a>
                            </li>
                            <li class="nav-item" style="white-space: nowrap;">
                                <a class="nav-link a-tab" href="#potongan">Potongan</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content py-4 px-1">
                        <div class="tab-pane active" id="semester" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Semester</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.prodi.semester.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Semester', '#AddSemester', getSemester)">
                                    Tambah Semester
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-semester">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="pembayaran-semester" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Pembayaran Semester</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.prodi.pembayaran.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Semester', '#PembayaranSemester', getSemesterPembayaran)">
                                    Tambah Biaya
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-pembayaran-semester">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Semester</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="pembayaran-lainnya" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Pembayaran Lainnya</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.prodi.pembayaran-lainnya.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Pembayaran Lainnya', '#PembayaranLainnya', getJenisPembayaranLainnya)">
                                    Tambah Biaya
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-pembayaran-lainnya">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="potongan" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Potongan</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.prodi.potongan.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Potongan', '#Potongan')">
                                    Tambah
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-potongan">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Semester</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Actions</th>
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

    <div class="modal fade" id="AddSemester" tabindex="-1" aria-labelledby="AddSemesterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="SemesterLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" id="semester_id">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="PembayaranSemester" tabindex="-1" role="dialog" aria-labelledby="PembayaranSemesterLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-pembayaran-semester">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PembayaranSemesterLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester_pembayaran_id" class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" id="semester_pembayaran_id">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-pembayaran-semester" rows="3" name="ket"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="publish" class="form-label">Publish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="publish"
                                    value="1" id="publish">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tablePembayaranSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="PembayaranLainnya" tabindex="-1" role="dialog"
        aria-labelledby="PembayaranLainnyaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-pembayaran-lainnya">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PembayaranLainnyaLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pembayaran_lainnya_id" class="form-label">Jenis Pembayaran</label>
                            <select class="form-select" name="pembayaran_lainnya_id" id="pembayaran_lainnya_id">
                                <option value="">Pilih Jenis Pembayaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-pembayaran-lainnya" rows="3" name="ket"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="publish" class="form-label">Publish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="publish"
                                    value="1" id="publish">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tablePembayaranSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Potongan" tabindex="-1" role="dialog" aria-labelledby="PotonganLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-potongan">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PotonganLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="potongan_id" class="form-label">Potongan</label>
                            <select class="form-select" name="potongan_id" id="potongan_id">
                                <option value="">Pilih Potongan</option>
                                @foreach ($potongan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="tahun_semester_id" id="tahun_semester_id">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesterPotongan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-potongan" rows="3" name="ket"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="publish" class="form-label">Publish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="publish"
                                    value="1" id="publish">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tablePotongan.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let tableSemester, tablePembayaranSemester, tablePembayaranLainnya, tablePotongan;

        function getSemester() {
            $('#semester_id').attr('disabled', 'disabled');
            $.ajax({
                type: "GET",
                url: "{{ route('data-master.prodi.semester.index', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                success: function(res) {
                    $('#semester_id').empty().append('<option value="">Pilih Semester</option>')
                    $.each(res.data, function(i, e) {
                        $('#semester_id').append(
                            `<option value="${e.id}">${e.nama}</option>`
                        )
                    })
                    $('#semester_id').removeAttr('disabled');
                },
                error: function() {
                    console.log('Gagal get semester');
                    $('#semester_id').removeAttr('disabled');
                }
            })
        }

        function getSemesterPembayaran(data = {}) {
            $('#semester_pembayaran_id').attr('disabled', 'disabled');
            $.ajax({
                type: "GET",
                url: "{{ route('data-master.prodi.pembayaran.getSemester', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                data: {
                    tahun_semester_id: data.tahun_semester_id
                },
                success: function(res) {
                    $('#semester_pembayaran_id').empty().append('<option value="">Pilih Semester</option>')
                    $.each(res.data, function(i, e) {
                        $('#semester_pembayaran_id').append(
                            `<option value="${e.id}">${e.nama}</option>`
                        )
                    })

                    if (data.tahun_semester_id) {
                        $('#semester_pembayaran_id').val(data.tahun_semester_id);
                    } else {
                        $('#semester_pembayaran_id').removeAttr('disabled');
                    }
                },
                error: function() {
                    console.log('Gagal get semester');
                    $('#semester_pembayaran_id').removeAttr('disabled');
                }
            })
        }

        function getJenisPembayaranLainnya(data = {}) {
            $('#pembayaran_lainnya_id').attr('disabled', 'disabled');
            $.ajax({
                type: "GET",
                url: "{{ route('data-master.prodi.pembayaran-lainnya.getJenis', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                data: {
                    pembayaran_lainnya_id: data.pembayaran_lainnya_id
                },
                success: function(res) {
                    $('#pembayaran_lainnya_id').empty().append(
                        '<option value="">Pilih Jenis Pembayaran</option>')
                    $.each(res.data, function(i, e) {
                        $('#pembayaran_lainnya_id').append(
                            `<option value="${e.id}">${e.nama}</option>`
                        )
                    })

                    if (data.pembayaran_lainnya_id) {
                        $('#pembayaran_lainnya_id').val(data.pembayaran_lainnya_id);
                    } else {
                        $('#pembayaran_lainnya_id').removeAttr('disabled');
                    }
                },
                error: function() {
                    console.log('Gagal get jenis pembayaran lainnya');
                    $('#pembayaran_lainnya_id').removeAttr('disabled');
                }
            })
        }

        $(document).ready(function() {
            tableSemester = $('.table-semester').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.prodi.semester.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "tgl_mulai"
                    },
                    {
                        "data": "tgl_selesai"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            tablePembayaranSemester = $('.table-pembayaran-semester').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.prodi.pembayaran.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "semester"
                    },
                    {
                        "data": "nominal"
                    },
                    {
                        "data": "publish"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            tablePembayaranLainnya = $('.table-pembayaran-lainnya').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.prodi.pembayaran-lainnya.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "jenis"
                    },
                    {
                        "data": "nominal"
                    },
                    {
                        "data": "publish"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            tablePotongan = $('.table-potongan').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.prodi.potongan.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "potongan"
                    },
                    {
                        "data": "semester"
                    },
                    {
                        "data": "nominal"
                    },
                    {
                        "data": "publish"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
