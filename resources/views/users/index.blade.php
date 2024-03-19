@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-2">
                            <h5 class="text-capitalize">{{ request('role') }}</h5>
                        </div>
                        <div class="col-md-10">
                            @can('add_users')
                                <div class="row justify-content-end align-items-center pe-3" style="gap: 1rem">
                                    @if (request('role') == 'mahasiswa')
                                        <div class="col-md-3 px-0">
                                            <select class="form-select" id="filter-prodi">
                                                <option value="" selected>Pilih Prodi</option>
                                                @foreach ($prodis as $prodi)
                                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 px-0">
                                            <select class="form-select" id="filter-tahun-ajaran">
                                                <option value="" selected>Pilih Tahun Masuk</option>
                                                @foreach ($tahun_ajarans as $tahun_ajaran)
                                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 px-0">
                                            <select class="form-select" id="filter-rombel">
                                                <option value="" selected>Pilih Rombel</option>
                                                @foreach ($rombels as $rombel)
                                                    <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 px-0">
                                            <form action="{{ route('kelola-users.exportPembayaran', request('role')) }}" class="form-export">
                                                <input type="hidden" name="prodi">
                                                <input type="hidden" name="tahun_ajaran">
                                                <button type="button"
                                                    class="btn btn-primary w-100">Export Pembayaran</button>
                                            </form>
                                        </div>
                                        {{-- <div class="col-md-1 px-0">
                                            <a href="{{ route('users.import', request('role')) }}"
                                                class="btn btn-primary d-block">Import</a>
                                        </div> --}}
                                    @endif
                                    <div class="col-md-2 px-0">
                                        <a href="{{ route('kelola-users.create', ['role' => request('role')]) }}"
                                            class="btn btn-primary d-block text-capitalize">Tambah</a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    @if (request('role') == 'mahasiswa')
                                        <th>NIM</th>
                                    @elseif(request('role') == 'dosen' || request('role') == 'asdos')
                                        <th>NIDN</th>
                                    @else
                                        <th>Email</th>
                                    @endcan
                                    @can('edit_users', 'delete_users')
                                        <th>Aksi</th>
                                    @endif
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
    </script>
    <script>
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-users.data', request('role')) }}',
                    @if (request('role') == 'mahasiswa')
                        data: function(p) {
                            p.prodi = $('#filter-prodi').val();
                            p.tahun_ajaran = $('#filter-tahun-ajaran').val();
                            p.rombel = $('#filter-rombel').val();
                        }
                    @endif
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
                    @can('edit_users', 'hapus_users')
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
    @if (request('role') == 'mahasiswa')
        <script>
            $('#filter-prodi, #filter-tahun-ajaran, #filter-rombel').on('change', function() {
                table.ajax.reload();
            });

            $('.form-export button').on('click', function() {
                $('.form-export input[name="prodi"]').val($('#filter-prodi').val());
                $('.form-export input[name="rombel"]').val($('#filter-rombel').val());
                $('.form-export input[name="tahun_ajaran"]').val($('#filter-tahun-ajaran').val());
                $('.form-export').submit();
            })
        </script>
    @endif
@endpush
