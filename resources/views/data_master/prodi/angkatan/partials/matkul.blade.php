@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Mata Kuliah</h5>
                        @can('add_matkul')
                            <button type="button" class="btn btn-primary"
                                onclick="addForm('{{ route('data-master.tahun-ajaran.matkul.store', request('id')) }}', 'Tambah Mata Kuliah', '#Matkul')">
                                Tambah
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-4">
                        <select id="filter-prodi" class="form-control mb-3">
                            <option value="">Pilih Prodi</option>
                            @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Kurikulum</th>
                                    <th>Dosen</th>
                                    <th>Rombel</th>
                                    @can('edit_matkul', 'delete_matkul')
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
@endsection

@can('add_matkul')
    <div class="modal fade" id="Matkul" tabindex="-1" role="dialog" aria-labelledby="MatkulLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-matkul">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="MatkulLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="div-alert"></div>
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select class="form-select" name="prodi_id" id="prodi_id"
                                onchange="get_kurikulum(); get_rombel();">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kurikulum_id" class="form-label">Kurikulum</label>
                            <select class="form-select" name="kurikulum_id" id="kurikulum_id" onchange="get_matkul()">
                                <option value="">Pilih Kurikulum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="matkul_id" class="form-label">Mata Kuliah</label>
                            <select class="form-select" name="matkul_id" id="matkul_id">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen</label>
                            <select class="form-select select2" style="width: 100%;" name="dosen_id[]" multiple
                                id="dosen_id">
                                @foreach ($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->login_key }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ruang_id" class="form-label">Ruang</label>
                            <select class="form-select select2" name="ruang_id[]" id="ruang_id" multiple
                                style="width: 100%">
                                @foreach ($ruangs as $ruang)
                                    <option value="{{ $ruang->id }}">{{ $ruang->nama }} (Kapasitas:
                                        {{ $ruang->kapasitas }})
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
                                    <input class="form-control" type="time" id="jam_mulai" placeholder="Jam Mulai"
                                        name="jam_mulai" min="00:00" max="24:00" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_akhir" class="form-label">Jam Akhir</label>
                                    <input class="form-control" type="time" id="jam_akhir" placeholder="Jam akhir"
                                        name="jam_akhir" min="00:00" max="24:00" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rombel_id" class="form-label">Rombel</label>
                            <select class="form-select select2" name="rombel_id[]" id="rombel_id" style="width: 100%"
                                multiple>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cek_ip" class="form-label">Cek IP?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="cek_ip"
                                    value="1" id="cek_ip">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableMatkul.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@push('js')
    <script>
        function get_kurikulum(data = {}) {
            let id = $('#prodi_id').val();
            console.log(id)
            $('#kurikulum_id').empty().append('<option value="">Pilih Kurikulum</option>')
            $.ajax({
                url: "{{ route('data-master.tahun-ajaran.matkul.getKurikulum', ['id' => request('id'), 'prodi_id' => ':id']) }}"
                    .replace(':id', id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(key, value) {
                        $('#kurikulum_id').append(`<option value="${value.id}">${value.nama}</option>`);
                    })

                    if (data.kurikulum_id) {
                        $('#kurikulum_id').val(data.kurikulum_id);
                        get_matkul(data)
                        get_rombel(data)
                    }
                },
                error: function(err) {
                    alert('Gagal get matkul')
                }
            })
        }

        function get_matkul(data = {}) {
            let id = $('#kurikulum_id').val();
            $('#matkul_id').empty().append(
                '<option value="">Pilih Mata Kuliah</option>'
            );
            $.ajax({
                url: "{{ route('data-master.tahun-ajaran.matkul.getMatkul', ['id' => request('id'), 'kurikulum_id' => ':id']) }}"
                    .replace(':id', id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(key, value) {
                        $('#matkul_id').append(`<option value="${value.id}">${value.nama}</option>`);
                    })

                    if (data.matkul_id) {
                        $('#matkul_id').val(data.matkul_id)
                    }
                },
                error: function(err) {
                    alert('Gagal get matkul')
                }
            })
        }

        function get_rombel(data = {}) {
            let id = $('#prodi_id').val();
            $('#rombel_id').empty();

            if (id && id !== '') {
                $.ajax({
                    url: "{{ route('data-master.tahun-ajaran.matkul.getRombel', ['id' => request('id'), 'prodi_id' => ':id']) }}"
                        .replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(key, value) {
                            $('#rombel_id').append(
                                `<option value="${value.id}">${value.nama}</option>`);
                        })

                        if (data.rombel_id) {
                            $('#rombel_id').val(data.rombel_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get rombel')
                    }
                })
            }
        }

        let tableMatkul;

        $(document).ready(function() {
            tableMatkul = $('.table-matkul').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.tahun-ajaran.matkul.data', request('id')) }}',
                    data: function(d) {
                        d.prodi_id = $('#filter-prodi').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "kurikulum"
                    },
                    {
                        "data": "dosen"
                    },
                    {
                        "data": "rombel"
                    },
                    @can('edit_matkul', 'delete_matkul')
                        {
                            "data": "options"
                        }
                    @endcan
                ],
                pageLength: 25,
            });
        });

        $('#filter-prodi').on('change', function() {
            tableMatkul.ajax.reload();
        });
    </script>
@endpush
