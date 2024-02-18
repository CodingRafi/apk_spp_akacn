@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Set Matkul</h5>
                    @can('add_krs')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-pembayaran.potongan.store') }}', 'Tambah', '#potongan')">
                            Tambah
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
                                    @can('add_krs')
                                        <th>Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="krs" tabindex="-1" aria-labelledby="krsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="get">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="krsLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @method('post')
                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen</label>
                            <select class="form-select" name="dosen_id" id="dosen_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="matkul_id" class="form-label">Matkul</label>
                            <select class="form-select" name="matkul_id" id="matkul_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($matkuls as $matkul)
                                    <option value="{{ $matkul->id }}">{{ $matkul->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ruang_id" class="form-label">Ruang</label>
                            <select class="form-select" name="ruang_id" id="ruang_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($ruangs as $ruang)
                                    <option value="{{ $ruang->id }}">{{ $ruang->nama }} ({{ $ruang->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select class="form-select" name="hari" id="hari">
                                <option value="">Pilih Hari</option>
                                @foreach (config('services.hari') as $key => $hari)
                                    <option value="{{ $key }}">{{ $hari }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <input class="form-control" type="text" id="jam_mulai" placeholder="Jam Mulai" name="jam_mulai" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_akhir" class="form-label">Jam Akhir</label>
                                    <input class="form-control" type="text" id="jam_akhir" placeholder="Jam akhir" name="jam_akhir" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => table.ajax.reload())">Simpan</button>
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
                ajax: {
                    url: '{{ route('kelola-pembayaran.potongan.data') }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    @can('edit_krs', 'delete_krs')
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
