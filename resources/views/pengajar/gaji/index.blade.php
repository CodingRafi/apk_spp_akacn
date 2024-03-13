@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Gaji</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tunjangan</th>
                                    <th>Total Kehadiran</th>
                                    <th>Uang Transport</th>
                                    <th>Total Uang Transport</th>
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
                ajax: '{{ route('gaji.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "tunjangan"
                    },
                    {
                        "data": "fee_transport"
                    },
                    {
                        "data": "total_fee_transport"
                    },
                    {
                        "data": "total_fee_transport"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
