@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-nilai.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Nilai</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="tahun_matkul_id" class="form-control mb-3">
                                <option value="">Pilih Matkul</option>
                                @foreach ($matkul as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} | {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_semester_id" class="form-control mb-3">
                                <option value="">Pilih Semester</option>
                                @foreach ($semester as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" aria-label="Data rombel">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
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
                    url: '{{ route('kelola-nilai.getRombel', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                    data: function(p) {
                        p.tahun_semester_id = $('#tahun_semester_id').val();
                        p.tahun_matkul_id = $('#tahun_matkul_id').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });

        $('#tahun_matkul_id, #tahun_semester_id').on('change', function() {
            table.ajax.reload();
        });
    </script>
@endpush
