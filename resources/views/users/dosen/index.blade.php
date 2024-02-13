@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-capitalize">Dosen</h5>
                        @can('add_users')
                            <a href="{{ route('kelola-users.dosen.create') }}" class="btn btn-primary text-capitalize" style="width: fit-content;">Tambah</a>
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
                                    <th>Email</th>
                                    @can('edit_users', 'delete_users')
                                        <th>Actions</th>
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
    {{-- <script>
        let table;
    </script>
    <script>
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('users.data', request('role')) }}',
                    @if (request('role') == 'mahasiswa')
                        data: function(p) {
                            p.prodi = $('#filter-prodi').val();
                            p.tahun_ajaran = $('#filter-tahun-ajaran').val();
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
                        "data": "email"
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
            $('#filter-prodi, #filter-tahun-ajaran').on('change', function() {
                table.ajax.reload();
            });

            $('.form-export button').on('click', function() {
                $('.form-export input[name="prodi"]').val($('#filter-prodi').val());
                $('.form-export input[name="tahun_ajaran"]').val($('#filter-tahun-ajaran').val());
                $('.form-export').submit();
            })
        </script>
    @endif --}}
@endpush
