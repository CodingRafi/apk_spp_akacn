@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-users.index', ['role' => request('role')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Potongan Mahasiswa</h5>
                    </div>
                    <button class="btn btn-primary" type="button"
                        onclick="addForm('{{ route('kelola-users.potongan.store', ['user_id' => request('user_id'), 'role' => request('role')]) }}', 'Tambah Potongan', '#addPotongan', getPotongan)">Tambah</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Untuk</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPotongan" tabindex="-1" aria-labelledby="addPotonganLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="get">
                @method('post')
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addPotonganLabel">Set Potongan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="potongan_id" class="form-label">Potongan</label>
                            <select class="form-select select2" name="potongan_id[]" multiple style="width: 100%;"
                                id="potongan_id">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    {{-- @include('potongan.js') --}}
    <script>
        let table;

        function getPotongan() {
            $('#potongan_id').empty().attr('disabled', 'disabled');
            $.ajax({
                url: "{{ route('kelola-users.potongan.get', ['user_id' => request('user_id'), 'role' => request('role')]) }}",
                type: "GET",
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#potongan_id').append(
                            `<option value="${e.id}">${e.nama} (${parseInt(e.nominal).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })})</option>`
                        )
                    })
                    $('#potongan_id').removeAttr('disabled');
                },
                error: function() {
                    alert('Maaf telah terjadi kesalahan!')
                    $('#potongan_id').removeAttr('disabled');
                }
            })
        }
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('kelola-users.potongan.data', ['user_id' => request('user_id'), 'role' => request('role')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "potongan"
                    },
                    {
                        "data": "namaParse"
                    },
                    {
                        "data": "nominal"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        })
    </script>
@endpush
