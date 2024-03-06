@extends('mylayouts.main')

@section('container')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-capitalize">Prodi</h5>
                @can('add_prodi')
                <a href="{{ route('data-master.prodi.create') }}" class="btn btn-primary text-capitalize">Tambah Prodi</a>
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
    $(document).ready(function(){
        $('.table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route("data-master.prodi.data") }}',
            columns: [
                        { "data": "DT_RowIndex" },
                        { "data": "nama" },
                        { "data": "jenjang" },
                        { "data": "status" },
                        @can('edit_prodi', 'hapus_prodi')
                            { "data": "options" }
                        @endcan
                    ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
@endpush