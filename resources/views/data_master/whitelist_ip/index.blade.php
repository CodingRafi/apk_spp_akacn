@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Whitelist IP</h5>
                    @can('add_whitelist_ip')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIp">
                            Tambah IP
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>IP</th>
                                    @can('add_whitelist_ip')
                                        <th>Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addIp" tabindex="-1" aria-labelledby="addIpLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kelola-presensi.whitelist-ip.store') }}" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addIpLabel">Tambah IP</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control @error('nama') is-invalid @enderror" type="text" id="nama"
                                name="nama" />
                        </div>
                        <label for="ip" class="form-label">ip</label>
                        <div class="row gap-3 p-2">
                            <input class="form-control @error('ip') is-invalid @enderror" type="text" id="ip"
                                name="ip" readonly />
                            <button class="btn btn-primary" type="button" onclick="get_ip()">Cek IP</button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function get_ip() {
            $.ajax({
                type: "GET",
                url: "{{ route('kelola-presensi.whitelist-ip.get-ip') }}",
                success: function(res) {
                    $('#ip').val(res.ip);
                    showAlert('IP berhasil didapatkan!', 'success')
                },
                error: function(err) {
                    showAlert('Gagal mendapatkan IP', 'error');
                }
            })
        }
    </script>
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('kelola-presensi.whitelist-ip.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "ip"
                    },
                    @can('delete_whitelist_ip')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
