@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-gaji.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Detail</h5>
                    </div>
                    @if (!$data->status)
                        <form action="{{ route('kelola-gaji.publish', $data->id) }}" method="post">
                            @csrf
                            @method('patch')
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </form>
                    @else
                        <form action="{{ route('kelola-gaji.unpublish', $data->id) }}" method="post">
                            @csrf
                            @method('patch')
                            <button type="submit" class="btn btn-danger">Unpublish</button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @if (!$data->status)
                        <div class="alert alert-warning">
                            Gaji ini belum dipublish
                        </div>
                    @else
                        <div class="alert alert-success">
                            Gaji ini sudah dipublish
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Tunjangan</th>
                                    <th>Biaya Transport</th>
                                    <th>Total Kehadiran</th>
                                    <th>Total Biaya Transport</th>
                                    <th>Total Gaji</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gaji" tabindex="-1" aria-labelledby="gajiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="get">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="gajiLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tgl_awal" class="form-label">Tanggal Awal</label>
                            <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
                            <input class="form-control" type="date" id="tgl_akhir" name="tgl_akhir" />
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this)">Generate</button>
                    </div>
                </form>
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
                ajax: '{{ route('kelola-gaji.dataDetail', ['id' => request('id')]) }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "tunjangan"
                    },
                    {
                        "data": "fee_transport"
                    },
                    {
                        "data": "total_kehadiran"
                    },
                    {
                        "data": "total_fee_transport"
                    },
                    {
                        "data": "total"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
