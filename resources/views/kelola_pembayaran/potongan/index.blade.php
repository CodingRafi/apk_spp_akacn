@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Potongan</h5>
                    @can('add_potongan')
                        <a href="{{ route('data-master.potongan.create') }}" class="btn btn-primary text-capitalize">Tambah
                            Potongan</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select id="filter-tahun-ajaran" class="form-control">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahun_ajarans as $tahun_ajaran)
                                <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filter-prodi" class="form-control">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filter-semester" class="form-control">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Prodi</th>
                                    <th>Semester</th>
                                    @can('add_potongan')
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
    <script>
        $(document).ready(function() {
            let table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('data-master.potongan.data') }}',
                    data: function(p) {
                        p.semester = $('#filter-semester').val();
                        p.prodi = $('#filter-prodi').val();
                        p.tahun_ajaran = $('#filter-tahun-ajaran').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "nominal"
                    },
                    {
                        "data": "tahun_ajaran"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "semester"
                    },
                    @can('edit_potongan', 'delete_potongan')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });

            $('#filter-semester, #filter-prodi, #filter-tahun-ajaran').on('change', function() {
                table.ajax.reload();
            });
        });

        $('#filter-prodi').on('change', function() {
            $('#filter-semester').empty().append('<option value="">Pilih Semester</option>');
            if ($(this).val()) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('data-master.potongan.getSemester', ':id') }}".replace(':id', $(this)
                        .val()),
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-semester').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        showAlert(err.responseJSON.message, 'error');
                    }
                })
            }
        })
    </script>
    @include('potongan.js')
@endpush
