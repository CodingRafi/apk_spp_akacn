@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Kelola Gaji</h5>
                    @can('add_kelola_gaji')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-gaji.store') }}', 'Generate Gaji', '#gaji')">
                            Generate Gaji
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Awal</th>
                                    <th>Tanggal Akhir</th>
                                    @canany(['edit_kelola_gaji'])
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

    <div class="modal fade" id="gaji" tabindex="-1" aria-labelledby="gajiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="gajiLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tgl_awal" class="form-label">Tanggal Awal</label>
                            <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
                            <input class="form-control" type="date" id="tgl_akhir" name="tgl_akhir" />
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Generate</button>
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
                ajax: '{{ route('kelola-gaji.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "tgl_awal"
                    },
                    {
                        "data": "tgl_akhir"
                    },
                    @canany(['edit_kelola_gaji'])
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
