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
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <button class="btn btn-warning btn-generate">Generate Ulang</button>
                            <form action="{{ route('kelola-gaji.publish', $data->id) }}" method="post">
                                @csrf
                                @method('patch')
                                <button type="submit" class="btn btn-primary">Publish</button>
                            </form>
                        </div>
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
                                    <th>Biaya 1 SKS</th>
                                    <th>Total Kehadiran</th>
                                    <th>Total Biaya SKS</th>
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
                        "data": "fee_sks"
                    },
                    {
                        "data": "total_kehadiran"
                    },
                    {
                        "data": "total_fee_matkul"
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

            function generate_ulang() {
                $.LoadingOverlay("show");
                $.ajax({
                    url: '{{ route('kelola-gaji.generateUlang', ['id' => request('id')]) }}',
                    type: 'get',
                    dataType: 'json',
                    success: function(res) {
                        $.LoadingOverlay("hide");
                        showAlert(res.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(err) {
                        $.LoadingOverlay("hide");
                        showAlert('Gagal generate ulang', 'error');
                    }
                })
            }

            $('.btn-generate').on('click', function() {
                Swal.fire({
                    title: 'Apakah anda yakin akan generate ulang?',
                    text: 'Klik "Ya" jika setuju, klik "Tidak" jika tidak setuju',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generate_ulang();
                    }
                });
            })
        });
    </script>
@endpush
