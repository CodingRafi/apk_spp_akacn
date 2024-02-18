@extends('mylayouts.main')

@section('container')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <a href="{{ route('data-master.prodi.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                <h5 class="text-capitalize mb-0">Prodi {{ $prodi->nama }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Angkatan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Angkatan</th>
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
    $(document).ready(function(){
        $('.table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route("data-master.prodi.angkatan", request("prodi")) }}',
            columns: [
                        { "data": "DT_RowIndex" },
                        { "data": "nama" },
                        { "data": "options" }
                    ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
@endpush