@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <a href="{{ route('data-master.mata-kuliah.index') }}">
                            <i class="menu-icon tf-icons bx bx-chevron-left"></i>
                        </a>
                        <h5 class="text-capitalize mb-0">Materi</h5>
                    </div>
                    @can('add_materi')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.mata-kuliah.materi.store', ['matkul_id' => request('matkul_id')]) }}', 'Materi', '#materi')">
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
                                    <th>Materi</th>
                                    @canany(['edit_materi', 'delete_materi'])
                                        <th>Aksi</th>
                                    @endcanany
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="materi" tabindex="-1" aria-labelledby="materiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="materiLabel">Tambah Materi</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="materi" class="form-label">materi</label>
                            <input class="form-control" type="text" id="materi" name="materi" />
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
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.mata-kuliah.materi.data', ['matkul_id' => request('matkul_id')]) }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "materi"
                    },
                    @canany(['edit_materi', 'delete_materi'])
                        {
                            "data": "options"
                        }
                    @endcanany
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
