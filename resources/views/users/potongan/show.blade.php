@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('users.potongan.index', ['role' => request('role'), 'id' => request('id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Potongan {{ $semester->nama }}</h5>
                    </div>
                    @can('edit_users')
                    <a href="{{ route('users.potongan.create', ['role' => request('role'), 'id' => request('id'), 'semester_id' => request('semester_id')]) }}" class="btn btn-primary text-capitalize">Set Potongan</a>
                    @endcan
                </div>
                <div class="card-body">
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
                                    <th>Action</th>
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
        $(document).ready(function() {
            let table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route("users.potongan.data", ["role" => request("role"), "id" => request("id"), "semester_id" => request("semester_id")]) }}',
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
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
    @include('potongan.js')
@endpush
