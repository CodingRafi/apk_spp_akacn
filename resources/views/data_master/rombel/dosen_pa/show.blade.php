@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('data-master.rombel.dosen-pa.index', ['rombel_id' => request('rombel_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Set Dosen PA Rombel</h5>
                    </div>
                    @can('add_rombel')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.rombel.dosen-pa.store', ['rombel_id' => request('rombel_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Set Dosen PA', '#dosenPa', getDosen)">
                            Tambah Dosen PA
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Dosen PA</th>
                                    <th>Mahasiswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="dosenPa" tabindex="-1" aria-labelledby="dosenPaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="dosenPaLabel">Tambah Dosen PA</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @method('post')
                        <div class="mb-3">
                            <label for="dosen_pa" class="form-label">Dosen</label>
                            <select class="form-select select2" name="dosen_pa_id[]" multiple style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mahasiswa" tabindex="-1" aria-labelledby="mahasiswaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mahasiswaLabel">Set Mahasiswa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @method('patch')
                        <div class="mb-3">
                            <label for="mhs_id" class="form-label">Mahasiswa</label>
                            <select class="form-select select2" name="mhs_id[]" multiple style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function getDosen() {
            $.ajax({
                url: '{{ route('data-master.rombel.dosen-pa.getDosen', ['rombel_id' => request('rombel_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('select[name="dosen_pa_id[]"]').empty();
                    $.each(data, function(key, value) {
                        $('select[name="dosen_pa_id[]"]').append(
                            `<option value="${value.id}">${value.name} (${value.login_key})</option>`
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function getMhs(data){
            $.ajax({
                url: '{{ route('data-master.rombel.dosen-pa.listMahasiswa', ['rombel_id' => request('rombel_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'dosen_id' => ':dosen_id']) }}'.replace(':dosen_id', data.dosen_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('select[name="mhs_id[]"]').empty();
                    $.each(res.data, function(key, value) {
                        $('select[name="mhs_id[]"]').append(
                            `<option value="${value.id}">${value.name} (${value.login_key})</option>`
                        );
                    });

                    if (data.mhs) {
                        $('select[name="mhs_id[]"]').val(data.mhs);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }
    </script>
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('data-master.rombel.dosen-pa.data', ['rombel_id' => request('rombel_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [
                    {
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "mhs"
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
@endpush
