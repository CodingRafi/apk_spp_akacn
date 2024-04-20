@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('data-master.prodi.angkatan.detail', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Set Mahasiswa MBKM</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('data-master.prodi.mbkm.mahasiswa.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}', 'Tambah Mahasiswa', '#mahasiswa', getMhs)">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mahasiswa" tabindex="-1" aria-labelledby="mahasiswaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mahasiswaLabel">Tambah Mahasiswa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="mhs_id" class="form-label">Mahasiswa</label>
                            <select name="mhs_id" id="mhs_id" class="form-select">
                                <option value="">Pilih Mahasiswa</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <select name="peran" id="peran" class="form-select">
                                <option value="">Pilih Peran</option>
                                @foreach (config('services.peran') as $key => $peran)
                                <option value="{{ $key }}">{{ $peran }}</option>
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
        function getMhs(data = {}){
            $('#mhs_id')
                .empty()
                .append(`<option value="">Pilih Mahasiswa</option>`)
                .attr('disabled', 'disabled');
                
            $.ajax({
                url: '{{ route('data-master.prodi.mbkm.mahasiswa.get-mhs', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}?except=' + data.mhs_id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(key, value) {
                        $('#mhs_id').append(`<option value="${value.id}">${value.name} (${value.login_key})</option>`);
                    });

                    if (data.mhs_id) {
                        $('#mhs_id').val(data.mhs_id);
                    }else{
                        $('#mhs_id').removeAttr('disabled');
                    }
                },
                error: function(err) {
                    alert('Gagal get mahasiswa');
                }
            })
        }
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('data-master.prodi.mbkm.mahasiswa.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "mhs"
                    },
                    {
                        "data": "peran"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
                responsive: true,
            });
        });
    </script>
@endpush
