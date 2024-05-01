@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-gaji.show', request('id')) }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Gaji Matkul</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Total Kehadiran Teori</th>
                                    <th>Fee SKS Teori</th>
                                    <th>Total Fee SKS Teori</th>
                                    <th>Total Kehadiran Praktek</th>
                                    <th>Fee SKS Praktek</th>
                                    <th>Total Fee SKS Praktek</th>
                                    <th>Total</th>
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
                ajax: '{{ route('kelola-gaji.dataMatkul', ['id' => request('id'), 'user_id' => request('user_id')]) }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "sks"
                    },
                    {
                        "data": "total_kehadiran_teori"
                    },
                    {
                        "data": "fee_sks_teori"
                    },
                    {
                        "data": "total_fee_sks_teori"
                    },
                    {
                        "data": "total_kehadiran_praktek"
                    },
                    {
                        "data": "fee_sks_praktek"
                    },
                    {
                        "data": "total_fee_sks_praktek"
                    },
                    {
                        "data": "total"
                    },
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
