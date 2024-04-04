@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Template Kuesioner</h5>
                    @can('add_ruang')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-kuesioner.template.store') }}', 'Kuesioner', '#kuesioner')">
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
                                    <th>Type</th>
                                    <th>Pertanyaan</th>
                                    <th>Status</th>
                                    @can('add_ruang')
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

    <div class="modal fade" id="kuesioner" tabindex="-1" aria-labelledby="kuesionerLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('kelola-kuesioner.template.store') }}" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="kuesionerLabel">Tambah Kuesioner</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Pilih Type</option>
                                <option value="input">Input</option>
                                <option value="choice">Pilihan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pertanyaan" class="form-label">Pertanyaan</label>
                            <textarea class="form-control textarea-tinymce" id="pertanyaan" rows="3" name="pertanyaan"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="status" value="1"
                                    id="status">
                            </div>
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
                ajax: '{{ route('kelola-kuesioner.template.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "type"
                    },
                    {
                        "data": "pertanyaan"
                    },
                    {
                        "data": "status"
                    },
                    @can('delete_ruang')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
                responsive: true,
            });
        });

        function change_status(e, url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    showAlert('Berhasil diubah!', 'success')
                },
                error: function(err) {
                    $(e).prop('checked', !$(e).prop('checked'));
                }
            })
        }
    </script>
@endpush
