@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Rombel</h5>
                    @can('add_rombel')
                        <button type="button" class="btn btn-primary" onclick="addForm('{{ route('data-master.rombel.store') }}', 'Rombel', '#rombel', storeRombel)">
                            Tambah Rombel
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
                                    <th>Prodi</th>
                                    <th>Jenis Kelas</th>
                                    @can('add_rombel')
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
    <div class="modal fade" id="rombel" tabindex="-1" aria-labelledby="rombelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="rombelLabel">Tambah Rombel</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @method('post')
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control @error('nama') is-invalid @enderror" type="text" id="nama"
                                name="nama" />
                        </div>
                        <div class="mb-3">
                            <label for="prodi" class="form-label">Prodi</label>
                            <select class="form-select" name="prodi_id" id="prodi_id">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelas_id" class="form-label">Jenis Kelas</label>
                            <select class="form-select" name="jenis_kelas_id" id="jenis_kelas_id">
                                <option value="">Pilih Jenis Kelas</option>
                                @foreach ($jenisKelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
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
        function storeRombel(){
            $('#prodi_id, #jenis_kelas_id').removeAttr('readonly');
        }

        function editRombel(){
            $('#prodi_id, #jenis_kelas_id').attr('readonly', 'readonly');
        }

        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.rombel.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "prodi"
                    },
                    {
                        "data": "jenis_kelas"
                    },
                    @can('edit_rombel', 'delete_rombel')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
            });

            $('#filter-semester, #filter-prodi, #filter-tahun-ajaran').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
