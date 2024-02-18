<div class="tab-pane" id="matkul" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>Mata Kuliah</h5>
        @can('add_matkul')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.matkul.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Mata Kuliah', '#Matkul')">
                Tambah
            </button>
        @endcan
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
                    @can('edit_matkul', 'delete_matkul')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

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
                        <div class="mb-3">
                            <label for="matkul_id" class="form-label">Mata Kuliah</label>
                            <select class="form-select" name="matkul_id" id="matkul_id" style="width: 100%">
                                @foreach ($kurikulums as $kurikulum)
                                    <optgroup label="{{ $kurikulum->nama }}">
                                        @foreach ($kurikulum->matkul as $matkul)
                                            <option value="{{ $matkul->id }}">{{ $matkul->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen</label>
                            <select class="form-select" name="dosen_id" id="dosen_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->login_key }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ruang_id" class="form-label">Ruang</label>
                            <select class="form-select" name="ruang_id" id="ruang_id">
                                <option value="">Pilih Ruang</option>
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
                                        name="jam_mulai" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jam_akhir" class="form-label">Jam Akhir</label>
                                    <input class="form-control" type="time" id="jam_akhir" placeholder="Jam akhir"
                                        name="jam_akhir" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cek_ip" class="form-label">Cek IP?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="cek_ip" value="1"
                                    id="cek_ip">
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

<script>
    let tableMatkul;

    $(document).ready(function() {
        tableMatkul = $('.table-matkul').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.matkul.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
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
                @can('edit_matkul', 'delete_matkul')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    });
</script>
