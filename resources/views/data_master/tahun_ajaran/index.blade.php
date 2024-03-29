@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize">Tahun Ajaran</h5>
                    <div class="d-flex justify-content-center align-items-center" style="gap: 1rem;">
                        @can('add_tahun_ajaran')
                            <button class="btn btn-primary" onclick="getData()">Get NEO Feeder</button>
                            <a href="{{ route('data-master.tahun-ajaran.create') }}"
                                class="btn btn-primary text-capitalize">Tambah
                                Tahun Ajaran</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    @can('edit_tahun_ajaran', 'delete_tahun_ajaran')
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
                ajax: '{{ route('data-master.tahun-ajaran.data') }}',
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
                        "data": "status"
                    },
                    @can('edit_tahun_ajaran', 'hapus_tahun_ajaran')
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
    @if (Auth::user()->hasRole('admin'))
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => 'tahun_ajaran',
            'urlStoreData' => route('data-master.tahun-ajaran.storeNeoFeeder'),
        ])
    @endif
@endpush
