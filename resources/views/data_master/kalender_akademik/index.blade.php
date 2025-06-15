@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Kalender Akademik</h5>
                    @can('add_kelola_kalender_akademik')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.kalender-akademik.store') }}', 'Kalender Akademik', '#kalender_akademik')">
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
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Selesai</th>
                                    <th>Keterangan</th>
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

    <div class="modal fade" id="kalender_akademik" tabindex="-1" aria-labelledby="kalender_akademikLabel" aria-hidden="true">
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
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input class="form-control" type="datetime-local" id="start_time" name="start_time" />
                        </div>
                        <div class="mb-3">
                            <label for="finish_time" class="form-label">Waktu Selesai</label>
                            <input class="form-control" type="datetime-local" id="finish_time" name="finish_time" />
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="comments" rows="3" name="comments"></textarea>
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
                        "data": "start_time"
                    },
                    {
                        "data": "finish_time"
                    },
                    {
                        "data": "comments"
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
