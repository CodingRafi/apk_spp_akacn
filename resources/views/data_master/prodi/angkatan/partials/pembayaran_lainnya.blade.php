<div class="tab-pane" id="pembayaran-lainnya" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>Pembayaran Lainnya</h5>
        @can('add_pembayaran_lainnya')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.pembayaran-lainnya.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Pembayaran Lainnya', '#PembayaranLainnya', getJenisPembayaranLainnya)">
                Tambah Biaya
            </button>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-pembayaran-lainnya">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    @can('edit_pembayaran_lainnya')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

@can('add_pembayaran_lainnya')
    <div class="modal fade" id="PembayaranLainnya" tabindex="-1" role="dialog" aria-labelledby="PembayaranLainnyaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-pembayaran-lainnya">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PembayaranLainnyaLabel">Tambah</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pembayaran_lainnya_id" class="form-label">Jenis Pembayaran</label>
                            <select class="form-select" name="pembayaran_lainnya_id" id="pembayaran_lainnya_id">
                                <option value="">Pilih Jenis Pembayaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-pembayaran-lainnya" rows="3" name="ket"></textarea>
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
                            onclick="submitForm(this.form, this, () => tablePembayaranSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<script>
    let tablePembayaranLainnya;

    function getJenisPembayaranLainnya(data = {}) {
        $('#pembayaran_lainnya_id').attr('disabled', 'disabled');
        $.ajax({
            type: "GET",
            url: "{{ route('data-master.prodi.pembayaran-lainnya.getJenis', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
            data: {
                pembayaran_lainnya_id: data.pembayaran_lainnya_id
            },
            success: function(res) {
                $('#pembayaran_lainnya_id').empty().append(
                    '<option value="">Pilih Jenis Pembayaran</option>')
                $.each(res.data, function(i, e) {
                    $('#pembayaran_lainnya_id').append(
                        `<option value="${e.id}">${e.nama}</option>`
                    )
                })

                if (data.pembayaran_lainnya_id) {
                    $('#pembayaran_lainnya_id').val(data.pembayaran_lainnya_id);
                } else {
                    $('#pembayaran_lainnya_id').removeAttr('disabled');
                }
            },
            error: function() {
                console.log('Gagal get jenis pembayaran lainnya');
                $('#pembayaran_lainnya_id').removeAttr('disabled');
            }
        })
    }

    $(document).ready(function() {
        tablePembayaranLainnya = $('.table-pembayaran-lainnya').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.pembayaran-lainnya.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "jenis"
                },
                {
                    "data": "nominal"
                },
                {
                    "data": "publish"
                },
                @can('edit_pembayaran_lainnya')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    })
</script>
