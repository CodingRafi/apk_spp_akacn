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
                                <a class="nav-link active a-tab" href="#semester">Semester</a>
                            </li>
                            <li class="nav-item" style="white-space: nowrap;">
                                <a class="nav-link a-tab" href="#pembayaran">Pembayaran</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content py-4 px-1">
                        <div class="tab-pane active" id="semester" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Semester</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.prodi.semesters.store') }}', 'Tambah Semester', '#AddSemester', getSemester)">
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="prodi_id" value="{{ request('prodi_id') }}">
                        <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
                        <div class="mb-3">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" id="semester_id">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => {})">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function getSemester() {
            $('#semester_id').attr('disabled', 'disabled');
            $.ajax({
                type: "GET",
                url: "{{ route('data-master.prodi.semesters', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
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

        $(document).ready(function() {
            let tableSemester = $('.table-semester').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('data-master.prodi.semesters.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
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
                    @can('edit_users', 'hapus_users')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
