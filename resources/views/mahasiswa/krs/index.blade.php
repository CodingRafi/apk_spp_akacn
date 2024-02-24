@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">KRS</h5>
                    {{-- <a href="{{ route('pembayaran.export') }}" class="btn btn-primary">Export</a> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Semester</th>
                                    <th>Jatah SKS</th>
                                    <th>SKS diambil</th>
                                    <th>Batas Isi KRS</th>
                                    <th>Status</th>
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
            $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('krs.dataSemester') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "jatah_sks"
                    },
                    {
                        "data": "sks_diambil"
                    },
                    {
                        "data": "tgl_pengisian"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
