@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Template Surat</h5>
                    @can('add_kelola_template_surat')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('data-master.template-surat.store') }}', 'Template Surat', '#template_surat')">
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
                                    <th>File</th>
                                    @canany(['edit_kelola_template_surat', 'delete_kelola_template_surat'])
                                    <th>Role</th>
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

    @can('add_kelola_template_surat')
    <div class="modal fade" id="template_surat" tabindex="-1" aria-labelledby="template_suratLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('data-master.template-surat.store') }}" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="template_suratLabel">Tambah Template Surat</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" />
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File</label>
                            <input class="form-control" type="file" id="file" name="file" />
                        </div>
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select name="role_id[]" id="role_id" class="select2" multiple style="width: 100%">
                                @foreach ($roles as $id => $role)
                                    <option value="{{ $id }}">{{ $role }}</option>
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
    @endcan
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.template-surat.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "file"
                    },
                    @canany(['edit_kelola_template_surat', 'delete_kelola_template_surat'])
                    {
                        "data": "role"
                    },
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
