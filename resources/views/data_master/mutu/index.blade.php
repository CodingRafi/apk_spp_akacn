@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Mutu</h5>
                    @can('add_mutu')
                        <button class="btn btn-primary" onclick="getData()">Get Neo
                            Feeder</button>
                        {{-- <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.mutu.store') }}', 'Mutu', '#mutu')">
                            Tambah
                        </button> --}}
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" aria-label="table-mutu">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Program Studi</th>
                                    <th>Nama</th>
                                    <th>Nilai</th>
                                    <th>Bobot Minimum</th>
                                    <th>Bobot Maksimum</th>
                                    <th>Tanggal Mulai Efektif</th>
                                    <th>Tanggal Akhir Efektif</th>
                                    <th>Status</th>
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
                ajax: '{{ route('data-master.mutu.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "nilai"
                    },
                    {
                        "data": "bobot_minimum"
                    },
                    {
                        "data": "bobot_maksimum"
                    },
                    {
                        "data": "tanggal_mulai_efektif"
                    },
                    {
                        "data": "tanggal_akhir_efektif"
                    },
                    {
                        "data": "status"
                    }
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
    @if (Auth::user()->hasRole('admin'))
        <script>
            let thisPage = 'neo_feeder'
        </script>
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => 'mutu',
            'urlStoreData' => route('data-master.mutu.storeNeoFeeder'),
        ])
    @endif
@endpush
