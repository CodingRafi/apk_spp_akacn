@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('data-master.tahun-ajaran.matkul.index', ['id' => request('id')]) }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="mb-0">Rekap Kelas Perkuliahan</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-matkul" aria-label="Data matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Semester</th>
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
            $('.table-matkul').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data-master.tahun-ajaran.matkul.rekap.data', ['id' => request('id'), 'matkul_id' => request('matkul_id')]) }}",
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "semester"
                    },
                    {
                        "data": "options"
                    }
                ]
            });
        });
    </script>
@endpush
