<div class="tab-pane" id="mbkm" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>MBKM</h5>
        @can('add_kelola_mbkm')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.mbkm.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah MBKM', '#Mbkm')">
                Tambah
            </button>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-mbkm">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Total Mahasiswa</th>
                    @can('edit_kelola_mbkm', 'delete_kelola_mbkm')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

@can('add_kelola_mbkm')
    <div class="modal fade" id="Mbkm" tabindex="-1" role="dialog" aria-labelledby="MbkmLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-mbkm">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="MbkmLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_anggota" class="form-label">Jenis Anggota</label>
                            <select class="form-select" name="jenis_anggota" id="jenis_anggota">
                                <option value="">Pilih Jenis Anggota</option>
                                <option value="0">Personal</option>
                                <option value="1">Kelompok</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_aktivitas_id" class="form-label">Jenis Aktivitas</label>
                            <select class="form-select" name="jenis_aktivitas_id" id="jenis_aktivitas_id">
                                <option value="">Pilih Jenis Aktivitas</option>
                                @foreach ($jenisAktivitas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="tahun_semester_id" id="tahun_semester_id">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesterPotongan as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="textarea-mbkm" rows="3" name="ket"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi" class="form-label">lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi">
                        </div>
                        <div class="mb-3">
                            <label for="sk_tugas" class="form-label">SK Tugas</label>
                            <input type="text" class="form-control" id="sk_tugas" name="sk_tugas">
                        </div>
                        <div class="mb-3">
                            <label for="tgl_sk_tugas" class="form-label">Tanggal SK Tugas</label>
                            <input type="date" class="form-control" id="tgl_sk_tugas" name="tgl_sk_tugas">
                        </div>
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Mahasiswa</label>
                            <select class="select2" multiple name="mhs_id[]" id="user_id" style="width: 100%">
                                @foreach ($mhs as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }} | {{ $row->login_key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableMbkm.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<script>
    let tableMbkm;

    $(document).ready(function() {
        tableMbkm = $('.table-mbkm').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.mbkm.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "judul"
                },
                {
                    "data": "jml_mhs"
                },
                @can('edit_kelola_mbkm', 'delete_kelola_mbkm')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    })
</script>
