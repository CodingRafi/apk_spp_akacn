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
                                    value="{{ isset($data) ? $data->nama : old('nama') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_lulus" class="form-label">Jumlah SKS Lulus</label>
                                <input class="form-control" type="number" id="jml_sks_lulus" name="jml_sks_lulus"
                                    value="{{ isset($data) ? $data->jml_sks_lulus : old('jml_sks_lulus') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_wajib" class="form-label">Jumlah SKS Wajib</label>
                                <input class="form-control" type="number" id="jml_sks_wajib" name="jml_sks_wajib"
                                    value="{{ isset($data) ? $data->jml_sks_wajib : old('jml_sks_wajib') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="jml_sks_pilihan" class="form-label">Jumlah SKS Pilihan</label>
                                <input class="form-control" type="number" id="jml_sks_pilihan" name="jml_sks_pilihan"
                                    value="{{ isset($data) ? $data->jml_sks_pilihan : old('jml_sks_pilihan') }}" />
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start f1-buttons">
                                <button class="btn btn-primary btn-next btn-selanjutnya-1" type="button"
                                    onclick="submitForm(this.form, this, setKurikulum)">Simpan</button>
                            </div>
                        </fieldset>

                        <fieldset data-id="2">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Mata Kuliah</h5>
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('data-master.mata-kuliah.store') }}', 'Tambah', '#matkul')">
                                    Tambah
                                </button>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="matkulLabel">Tambah Matkul</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="kurikulum_id">
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select class="form-select" name="prodi_id" id="prodi_id">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input class="form-control" type="text" id="kode" name="kode" />
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" />
                        </div>
                        <div class="mb-3">
                            <label for="jenis_matkul" class="form-label">Jenis Matkul</label>
                            <select class="form-select" name="jenis_matkul" id="jenis_matkul">
                                <option value="">Pilih Jenis Matkul</option>
                                @foreach (config('services.matkul.jenis') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kel_matkul" class="form-label">Kelompok Matkul</label>
                            <select class="form-select" name="kel_matkul" id="kel_matkul">
                                <option value="">Pilih Kelompok Matkul</option>
                                @foreach (config('services.matkul.kelompok') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sks_mata_kuliah" class="form-label">SKS Mata Kuliah</label>
                            <input class="form-control" type="number" id="sks_mata_kuliah" name="sks_mata_kuliah"
                                value="0" min="0" />
                        </div>
                        <div class="mb-3">
                            <label for="sks_tatap_muka" class="form-label">SKS Tatap Muka</label>
                            <input class="form-control" type="number" id="sks_tatap_muka" name="sks_tatap_muka"
                                value="0" min="0" />
                        </div>
                        <div class="mb-3">
                            <label for="sks_praktek" class="form-label">SKS Praktek</label>
                            <input class="form-control" type="number" id="sks_praktek" name="sks_praktek"
                                value="0" min="0" />
                        </div>
                        <div class="mb-3">
                            <label for="sks_praktek_lapangan" class="form-label">SKS Praktek Lapangan</label>
                            <input class="form-control" type="number" id="sks_praktek_lapangan"
                                name="sks_praktek_lapangan" value="0" min="0" />
                        </div>
                        <div class="mb-3">
                            <label for="sks_simulasi" class="form-label">SKS Simulasi</label>
                            <input class="form-control" type="number" id="sks_simulasi" name="sks_simulasi"
                                value="0" min="0" />
                        </div>
                        <div class="mb-3">
                            <label for="ada_sap" class="form-label">ada SAP?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_sap" value="1"
                                        id="ada_sap_1">
                                    <label class="form-check-label" for="ada_sap_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_sap" value="0"
                                        id="ada_sap_0" checked>
                                    <label class="form-check-label" for="ada_sap_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_silabus" class="form-label">ada Silabus?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_silabus" value="1"
                                        id="ada_silabus_1">
                                    <label class="form-check-label" for="ada_silabus_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_silabus" value="0"
                                        id="ada_silabus_0" checked>
                                    <label class="form-check-label" for="ada_silabus_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_bahan_ajar" class="form-label">ada bahan ajar?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_bahan_ajar" value="1"
                                        id="ada_bahan_ajar_1">
                                    <label class="form-check-label" for="ada_bahan_ajar_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_bahan_ajar" value="0"
                                        id="ada_bahan_ajar_0" checked>
                                    <label class="form-check-label" for="ada_bahan_ajar_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_acara_praktek" class="form-label">ada acara praktek?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_acara_praktek"
                                        value="1" id="ada_acara_praktek_1">
                                    <label class="form-check-label" for="ada_acara_praktek_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_acara_praktek"
                                        value="0" id="ada_acara_praktek_0" checked>
                                    <label class="form-check-label" for="ada_acara_praktek_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ada_diklat" class="form-label">ada acara diklat?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_diklat" value="1"
                                        id="ada_diklat_1">
                                    <label class="form-check-label" for="ada_diklat_1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ada_diklat" value="0"
                                        id="ada_diklat_0" checked>
                                    <label class="form-check-label" for="ada_diklat_0">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_mulai_aktif" class="form-label">Tanggal mulai aktif</label>
                            <input class="form-control" type="date" id="tgl_mulai_aktif" name="tgl_mulai_aktif" />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_akhir_aktif" class="form-label">Tanggal akhir aktif</label>
                            <input class="form-control" type="date" id="tgl_akhir_aktif" name="tgl_akhir_aktif" />
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
@endsection

@push('js')
    <script>
        let kurikulum;
        let tableMatkul;
        let url_update = '{{ route('data-master.kurikulum.update', [':id']) }}';
        let url_matkul = '{{ isset($data) ? route('data-master.mata-kuliah.data', $data->id) : route('data-master.mata-kuliah.data', [':id']) }}'

        tableMatkul = $('.table-matkul').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: url_matkul,
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
            url_matkul = url_matkul.replace(':id', data.id);
            tableMatkul.ajax.url(url_matkul);
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
@endpush
