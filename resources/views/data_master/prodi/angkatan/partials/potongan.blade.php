<div class="tab-pane" id="potongan" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>Potongan</h5>
        @can('add_potongan')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.potongan.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Potongan', '#Potongan')">
                Tambah
            </button>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-potongan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Semester</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    @can('edit_potongan', 'delete_potongan')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

@can('add_potongan')
    <div class="modal fade" id="Potongan" tabindex="-1" role="dialog" aria-labelledby="PotonganLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-potongan">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PotonganLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="potongan_id" class="form-label">Potongan</label>
                            <select class="form-select" name="potongan_id" id="potongan_id">
                                <option value="">Pilih Potongan</option>
                                @foreach ($potongan as $item)
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
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-potongan" rows="3" name="ket"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="publish" class="form-label">Publish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="publish" value="1"
                                    id="publish">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tablePotongan.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<script>
    let tablePotongan;

    $(document).ready(function() {
        tablePotongan = $('.table-potongan').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.potongan.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "potongan"
                },
                {
                    "data": "semester"
                },
                {
                    "data": "nominal"
                },
                {
                    "data": "publish"
                },
                @can('edit_potongan', 'delete_potongan')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    })
</script>
