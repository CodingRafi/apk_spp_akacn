@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card" id="card-list-jadwal">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize mb-0">Jadwal</h5>
                    @can('add_kelola_presensi')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-presensi.jadwal.store') }}', 'Tambah Jadwal', '#jadwal', clearForm)">
                            Tambah
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-ajaran" class="select2"
                                style="width: 100%;">
                                <option value="">Pilih Angkatan</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Matkul</th>
                                    <th>Mata Kuliah</th>
                                    <th>Aksi</th>
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
                ajax: {
                    url: '{{ route('kelola-presensi.jadwal.data') }}',
                    data: function(p) {
                        p.tahun_ajaran_id = $('#filter-tahun-ajaran').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode_matkul"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            // Filter change events to reload DataTable
            $('#filter-tahun-ajaran')
                .on('change', function() {
                    table.ajax.reload();
                });
        });
    </script>
@endpush
