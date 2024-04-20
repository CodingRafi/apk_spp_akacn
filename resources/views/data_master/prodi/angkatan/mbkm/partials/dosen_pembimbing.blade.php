<div class="tab-pane" id="dosen-pembimbing" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="card-title">Dosen Pembimbing</h5>
        <button type="button" class="btn btn-primary"
            onclick="addForm('{{ route('data-master.prodi.mbkm.dosen-pembimbing.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}', 'Tambah Dosen Pembimbing', '#dosenPembimbingModal', getDosenPembimbing)">
            Tambah
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-dosen-pembimbing" aria-label="table-dosen-pembimbing" style="width: 100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Dosen</th>
                    <th>Pembimbing Ke</th>
                    <th>Kategori Kegiatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="dosenPembimbingModal" aria-labelledby="dosenPembimbingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                @method('post')
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="dosenPembimbingModalLabel">Tambah Dosen Pembimbing</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dosen_pembimbing_id" class="form-label">Dosen</label>
                        <select name="dosen_id" id="dosen_pembimbing_id" class="form-select select2-dosen-pembimbing"
                            style="width: 100%;">
                            <option value="">Pilih Dosen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pembimbing_ke" class="form-label">Pembimbing Ke</label>
                        <input type="text" class="form-control" name="pembimbing_ke" id="pembimbing_ke">
                    </div>
                    <div class="mb-3">
                        <label for="pembimbing_kategori_kegiatan_id" class="form-label">Kategori Kegiatan Id</label>
                        <select name="kategori_kegiatan_id" id="pembimbing_kategori_kegiatan_id"
                            class="form-select select2 select2-kategori-kegiatan-pembimbing" style="width: 100%">
                            <option value="">Pilih Kategori Kegiatan</option>
                            @foreach ($kategoriKegiatan as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-start px-3">
                    <button type="button" class="btn btn-primary"
                        onclick="submitForm(this.form, this, () => tableDosenPembimbing.ajax.reload())">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getDosenPembimbing(data = {}) {
        $('#dosen_pembimbing_id')
            .empty()
            .append(`<option value="">Pilih Dosen</option>`)
            .attr('disabled', 'disabled');

        $.ajax({
            url: '{{ route('data-master.prodi.mbkm.dosen-pembimbing.get-dosen', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}?except=' +
                (data.dosen_id ?? ''),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $.each(res, function(key, value) {
                    $('#dosen_pembimbing_id').append(
                        `<option value="${value.id}">${value.name} (${value.login_key})</option>`
                    );
                });

                if (data.dosen_id) {
                    $('#dosen_pembimbing_id').val(data.dosen_id);
                } else {
                    $('#dosen_pembimbing_id').removeAttr('disabled');
                }
            },
            error: function(err) {
                alert('Gagal get dosen pembimbing');
            }
        })
    }

    let tableDosenPembimbing;
    $(document).ready(function() {
        tableDosenPembimbing = $('.table-dosen-pembimbing').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('data-master.prodi.mbkm.dosen-pembimbing.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}',
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "dosen"
                },
                {
                    "data": "pembimbing_ke"
                },
                {
                    "data": "kategori_kegiatan"
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
