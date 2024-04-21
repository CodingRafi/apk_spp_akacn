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

    {{-- <div class="modal fade" id="mutu" tabindex="-1" aria-labelledby="mutuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mutuLabel">Tambah Mutu</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" />
                        </div>
                        <div class="mb-3">
                            <label for="nilai" class="form-label">Nilai</label>
                            <input class="form-control" type="number" id="nilai" name="nilai" min="0"
                                step="0.01" />
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="status" value="1"
                                    id="status">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
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
