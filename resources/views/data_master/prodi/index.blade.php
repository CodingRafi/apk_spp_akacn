@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize">Prodi</h5>
                    @can('add_prodi')
                        <div class="d-flex justify-content-center align-items-center" style="gap: 1rem;">
                            <button class="btn btn-primary" onclick="get()">Get Neo Feeder</button>
                            <a href="{{ route('data-master.prodi.create') }}" class="btn btn-primary text-capitalize">Tambah
                                Prodi</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jenjang</th>
                                    <th>Status</th>
                                    @can('edit_prodi', 'delete_prodi')
                                        <th>Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
                ajax: '{{ route('data-master.prodi.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "jenjang"
                    },
                    {
                        "data": "status"
                    },
                    @can('edit_prodi', 'hapus_prodi')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });
        });

        function get() {
            $.LoadingOverlay("show");
            $.ajax({
                url: '{{ route('neo-feeder.get', ['type' => 'prodi']) }}',
                success: function(res) {
                    showAlert(res.output, 'success')
                    $.LoadingOverlay("hide");
                    table.ajax.reload();
                },
                error: function(err) {
                    $.LoadingOverlay("hide");
                    showAlert(err.responseJSON.output, 'error')
                }
            })
        }
    </script>
@endpush
