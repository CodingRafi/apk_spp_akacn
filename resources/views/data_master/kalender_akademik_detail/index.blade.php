@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('data-master.kalender-akademik.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="mb-0">Kalender Akademik {{ $data->nama }}</h5>
                    </div>
                    @can('add_kelola_kalender_akademik')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.kalender-akademik-detail.store', ['kalender_akademik_id' => request('kalender_akademik_id')]) }}', 'Kalender Akademik', '#kalender_akademik')">
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
                        <h1 class="modal-title fs-5" id="kalender_akademikLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tgl" class="form-label">Tanggal</label>
                            <input class="form-control" type="text" id="tgl" name="tgl" />
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <input class="form-control" type="text" id="ket" name="ket" />
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
                ajax: '{{ route('data-master.kalender-akademik-detail.data', ['kalender_akademik_id' => request('kalender_akademik_id')]) }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "tgl"
                    },
                    {
                        "data": "ket"
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
