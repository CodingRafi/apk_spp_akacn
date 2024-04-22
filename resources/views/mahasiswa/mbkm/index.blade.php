@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">MBKM</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-mbkm" aria-label="table-mbkm">
                            <thead>
                                <tr>
                                    <th class="col-1">No</th>
                                    <th class="col-5">Judul</th>
                                    <th class="col-2">Tanggal Mulai</th>
                                    <th class="col-2">Tanggal Selesai</th>
                                    <th class="col-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
                ajax: '{{ route('mbkm.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "judul"
                    },
                    {
                        "data": "tanggal_mulai"
                    },
                    {
                        "data": "tanggal_selesai"
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
