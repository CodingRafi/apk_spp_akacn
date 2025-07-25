@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Kalender Akademik</h5>
                    @can('add_kelola_kalender_akademik')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.kalender-akademik.store', ['kalender_akademik_id' => request('kalender_akademik_id')]) }}', 'Kalender Akademik', '#kalender_akademik')">
                            Tambah
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Aktif</th>
                                    @canany(['edit_kelola_kalender_akademik', 'delete_kelola_kalender_akademik'])
                                        <th>Aksi</th>
                                    @endcanany
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kalender_akademik" tabindex="-1" aria-labelledby="kalender_akademikLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="kalender_akademikLabel">Tambah Kalender Akademik</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" />
                        </div>
                        <div>
                            <label for="is_active" class="form-label">Aktif</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1"
                                    id="is_active">
                            </div>
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
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.kalender-akademik.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "is_active"
                    },
                    @canany(['edit_kelola_kalender_akademik', 'delete_kelola_kalender_akademik'])
                        {
                            "data": "options"
                        }
                    @endcanany
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
