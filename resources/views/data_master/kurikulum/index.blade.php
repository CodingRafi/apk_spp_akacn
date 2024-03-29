@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Kurikulum</h5>
                    @can('add_kurikulum')
                        <div class="d-flex justify-content-center align-items-center" style="gap: 1rem">
                            <button class="btn btn-primary" onclick="getData()">Get NEO Feeder</button>
                            <a href="{{ route('data-master.kurikulum.create') }}" class="btn btn-primary text-capitalize">Tambah
                                Kurikulum</a>
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
                                    <th>Prodi</th>
                                    <th>Mulai Berlaku</th>
                                    <th>Sync Neo Feeder</th>
                                    @can('edit_kurikulum', 'delete_kurikulum')
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
                ajax: '{{ route('data-master.kurikulum.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "semester"
                    },
                    {
                        "data": "sync"
                    },
                    @can('edit_kurikulum', 'hapus_kurikulum')
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
            'type' => 'kurikulum',
            'urlStoreData' => route('data-master.kurikulum.storeNeoFeeder'),
        ])
    @endif
@endpush
