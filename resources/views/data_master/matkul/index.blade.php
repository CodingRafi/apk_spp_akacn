@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Mata Kuliah</h5>
                    @can('add_matkul')
                        <div class="d-flex justify-content-center align-items-center" style="gap: 1rem">
                            <button class="btn btn-primary" onclick="getData()">Get NEO Feeder</button>
                            {{-- <a href="{{ route('data-master.matkul.create') }}" class="btn btn-primary text-capitalize">Tambah Mata Kuliah</a> --}}
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode MK</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Sync Neo Feeder</th>
                                    @can('edit_matkul', 'delete_matkul')
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
                ajax: '{{ route('data-master.mata-kuliah.dataMatkul') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "sync"
                    },
                    @can('edit_matkul', 'hapus_matkul')
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
            'type' => 'matkul',
            'urlStoreData' => route('data-master.mata-kuliah.storeNeoFeeder'),
        ])
    @endif
@endpush
