@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('kelola-presensi.presensi.index') }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Detail</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Matkul</th>
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
                    url: '{{ route('kelola-presensi.presensi.getJadwal', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "tgl"
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
        });
    </script>
@endpush
