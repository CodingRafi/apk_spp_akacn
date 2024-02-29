@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-2">
                            <h5 class="text-capitalize">Verifikasi KRS</h5>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select class="form-select" id="filter-status">
                                <option value="pengajuan" selected>pengajuan</option>
                                <option value="diterima">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select class="form-select" id="filter-prodi">
                                <option value="" selected>Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select class="form-select" id="filter-tahun-ajaran">
                                <option value="" selected>Pilih Tahun Ajaran</option>
                                @foreach ($tahun_ajarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Status</th>
                                    <th>Semester</th>
                                    <th>DIverifikasi</th>
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
        $(document).ready(function() {
            let table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('verifikasi-krs.data') }}',
                    data: function(p) {
                        p.status = $('#filter-status').val();
                        p.prodi = $('#filter-prodi').val();
                        p.tahun_ajaran = $('#filter-tahun-ajaran').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nim"
                    },
                    {
                        "data": "nama_mhs"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "semester"
                    },
                    {
                        "data": "verify_id"
                    },
                    @can('edit_kelola_pembayaran')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });

            $('#filter-status, #filter-prodi, #filter-tahun-ajaran').on('change', function() {
                table.ajax.reload();
            });

            $('.form-export button').on('click', function() {
                $('.form-export input[name="status"]').val($('#filter-status').val());
                $('.form-export input[name="prodi"]').val($('#filter-prodi').val());
                $('.form-export input[name="tahun-ajaran"]').val($('#filter-tahun_ajaran').val());
                $('.form-export').submit();
            })
        });
    </script>
@endpush
