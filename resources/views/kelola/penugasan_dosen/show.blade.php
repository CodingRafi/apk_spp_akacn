@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-capitalize">Penugasan Dosen {{ request('tahun_ajaran_id') }}</h5>
                        <button type="button" class="btn btn-primary" onclick="getData()">Get Neo Feeder</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" aria-label="penugasan dosen">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIDN</th>
                                    <th>Program Studi</th>
                                    <th>No Surat Tugas</th>
                                    <th>Tanggal Surat Tugas</th>
                                    <th>TMT Surat Tugas</th>
                                    <th>Homebase?</th>
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
        let table
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('penugasan-dosen.data', request('tahun_ajaran_id')) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "login_key"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "nomor_surat_tugas"
                    },
                    {
                        "data": "tgl_create"
                    },
                    {
                        "data": "tgl_create"
                    },
                    {
                        "data": "a_sp_homebase"
                    },
                ],
                pageLength: 25,
            });
        });
    </script>
    @if (Auth::user()->hasRole('admin'))
        <script>
            let thisPage = 'neo_feeder'
        </script>
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => 'penugasan_dosen',
            'urlStoreData' => route('penugasan-dosen.storeNeoFeeder', request('tahun_ajaran_id')),
        ])
    @endif
@endpush
