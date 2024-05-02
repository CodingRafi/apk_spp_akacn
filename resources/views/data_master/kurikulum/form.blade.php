@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('data-master.kurikulum.index') }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Kurikulum</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($data) ? route('data-master.kurikulum.update', $data->id) : route('data-master.kurikulum.store') }}"
                        method="POST" class="form-kurikulum f1" data-status="{{ isset($data) ? 'true' : 'false' }}">
                        @csrf
                        @if (isset($data))
                            @method('patch')
                        @endif
                        <fieldset data-id="1">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input class="form-control" type="text" id="nama" name="nama"
                                    value="{{ isset($data) ? $data->nama : old('nama') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_lulus" class="form-label">Jumlah SKS Lulus</label>
                                <input class="form-control" type="number" id="jml_sks_lulus" name="jml_sks_lulus"
                                    value="{{ isset($data) ? $data->jml_sks_lulus : old('jml_sks_lulus') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_wajib" class="form-label">Jumlah SKS Wajib</label>
                                <input class="form-control" type="number" id="jml_sks_wajib" name="jml_sks_wajib"
                                    value="{{ isset($data) ? $data->jml_sks_wajib : old('jml_sks_wajib') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_pilihan" class="form-label">Jumlah SKS Pilihan</label>
                                <input class="form-control" type="number" id="jml_sks_pilihan" name="jml_sks_pilihan"
                                    value="{{ isset($data) ? $data->jml_sks_pilihan : old('jml_sks_pilihan') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_mata_kuliah_wajib" class="form-label">Jumlah SKS Mata Kuliah
                                    Wajib</label>
                                <input class="form-control" type="number" id="jml_sks_mata_kuliah_wajib"
                                    name="jml_sks_mata_kuliah_wajib"
                                    value="{{ isset($data) ? $data->jml_sks_mata_kuliah_wajib : old('jml_sks_mata_kuliah_wajib') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_mata_kuliah_pilihan" class="form-label">Jumlah SKS Mata Kuliah
                                    Pilihan</label>
                                <input class="form-control" type="number" id="jml_sks_mata_kuliah_pilihan"
                                    name="jml_sks_mata_kuliah_pilihan"
                                    value="{{ isset($data) ? $data->jml_sks_mata_kuliah_pilihan : old('jml_sks_mata_kuliah_pilihan') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="semester_id" class="form-label">Semester Mulai</label>
                                <select name="semester_id" id="semester_id" class="select2" disabled>
                                    <option value="">Pilih Tahun Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ isset($data) && $data->semester_id == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="prodi_id" class="form-label">Program Studi</label>
                                <select name="prodi_id" id="prodi_id" class="form-control" disabled>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($prodis as $item)
                                        <option value="{{ $item->id }}"
                                            {{ isset($data) && $data->prodi_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start f1-buttons">
                                <button class="btn btn-primary btn-next btn-selanjutnya-1" type="button"
                                    onclick="setKurikulum()">Lanjutkan</button>
                            </div>
                        </fieldset>

                        <fieldset data-id="2">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Mata Kuliah</h5>
                                <div class="d-flex justify-content-center align-items-center" style="gap: 1rem;">
                                    <button type="button" class="btn btn-primary" onclick="getNeoFeeder()">Get Neo
                                        Feeder</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="addForm('{{ route('data-master.kurikulum.storeMatkul') }}', 'Tambah Mata Kuliah', '#matkul', getMatkul)">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-matkul">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>SKS Mata Kuliah</th>
                                            <th>Prodi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="f1-buttons mb-3 mt-3">
                                <button type="button" class="btn float-start btn-secondary btn-previous">Kembali</button>
                                <button type="button" class="btn btn-selesai btn-primary">Selesai</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="matkul" tabindex="-1" aria-labelledby="matkulLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="matkulLabel">Mata Kuliah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="kurikulum_id">
                        <div class="mb-3">
                            <label for="matkul_id" class="form-label">Mata Kuliah</label>
                            <select name="matkul_id[]" id="matkul_id" class="select2" multiple style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => {tableMatkul.ajax.reload()})">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let kurikulum;
        let tableMatkul;
        let url_update = '{{ route('data-master.kurikulum.update', [':id']) }}';
        let url_get_matkul =
            '{{ isset($data) ? route('data-master.kurikulum.getMatkul', $data->id) : route('data-master.kurikulum.getMatkul', [':id']) }}';
        let url_data_matkul =
            '{{ isset($data) ? route('data-master.kurikulum.dataMatkul', $data->id) : route('data-master.kurikulum.dataMatkul', [':id']) }}';

        function getMatkul() {
            $('#matkul_id').empty();
            $.ajax({
                url: url_get_matkul,
                method: 'GET',
                success: function(res) {
                    $.each(res.data, function(key, value) {
                        $('#matkul_id').append(
                            `<option value="${value.id}">${value.kode} - ${value.nama}</option>`);
                    })
                },
                error: function(err) {
                    alert('Gagal get matkul')
                }
            })
        }

        tableMatkul = $('.table-matkul').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: url_data_matkul,
            },
            columns: [{
                    data: 'kode'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'sks_mata_kuliah'
                },
                {
                    data: 'prodi'
                },
                {
                    data: 'options',
                    searchable: false,
                    sortable: false
                },
            ]
        });

        function setKurikulum(data) {
            let status = $('.form-kurikulum').attr('data-status');
            $('.form-kurikulum')
                .attr('action', url_update.replace(':id', data.id))
                .attr('data-status', 'true')
                .append('<input type="hidden" name="_method" value="patch">');
            $('[name="kurikulum_id"]').val(data.id);
            kurikulum = data;
            if (status == 'false') {
                $('.btn-selanjutnya-1').trigger('click');
            }

            // Matkul
            url_get_matkul = url_get_matkul.replace(':id', data.id);
            url_data_matkul = url_data_matkul.replace(':id', data.id);
            tableMatkul.ajax.url(url_data_matkul);
            tableMatkul.ajax.reload()

            $(".form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-editor")
                .removeClass("is-invalid");
            $(".invalid-feedback").remove();
        }

        $('.btn-selesai').on('click', function() {
            window.location.href = '{{ route('data-master.kurikulum.index') }}'
        })
    </script>
    @include('mypartials.tab', ['form' => '.form-kurikulum'])
    @if (Auth::user()->hasRole('admin'))
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => 'kurikulum_matkul',
            'urlStoreData' => route('data-master.kurikulum.storeMatkulNeoFeeder'),
        ])
    @endif
    <script>
        const rawKurikulumMatkul = configNeoFeeder.kurikulum_matkul.raw;

        function getNeoFeeder() {
            rawKurikulumMatkul.filter = `id_kurikulum='${kurikulum.id}'`;
            getData(rawKurikulumMatkul, () => {tableMatkul.ajax.reload()});
        }
    </script>
@endpush
