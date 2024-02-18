<div class="tab-pane" id="pembayaran-semester" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>Pembayaran Semester</h5>
        @can('add_kelola_pembayaran')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.pembayaran.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Semester', '#PembayaranSemester', getSemesterPembayaran)">
                Tambah Biaya
            </button>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-pembayaran-semester">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Semester</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    @can('edit_kelola_pembayaran', 'delete_kelola_pembayaran')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

@can('add_kelola_pembayaran')
    <div class="modal fade" id="PembayaranSemester" tabindex="-1" role="dialog" aria-labelledby="PembayaranSemesterLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-pembayaran-semester">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="PembayaranSemesterLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester_pembayaran_id" class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" id="semester_pembayaran_id">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal">
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control textarea-tinymce" id="textarea-pembayaran-semester" rows="3" name="ket"></textarea>
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
    let tablePembayaranSemester;

    function getSemesterPembayaran(data = {}) {
        $('#semester_pembayaran_id').attr('disabled', 'disabled');
        $.ajax({
            type: "GET",
            url: "{{ route('data-master.prodi.pembayaran.getSemester', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
            data: {
                tahun_semester_id: data.tahun_semester_id
            },
            success: function(res) {
                $('#semester_pembayaran_id').empty().append('<option value="">Pilih Semester</option>')
                $.each(res.data, function(i, e) {
                    $('#semester_pembayaran_id').append(
                        `<option value="${e.id}">${e.nama}</option>`
                    )
                })

                if (data.tahun_semester_id) {
                    $('#semester_pembayaran_id').val(data.tahun_semester_id);
                } else {
                    $('#semester_pembayaran_id').removeAttr('disabled');
                }
            },
            error: function() {
                console.log('Gagal get semester');
                $('#semester_pembayaran_id').removeAttr('disabled');
            }
        })
    }

    $(document).ready(function() {
        tablePembayaranSemester = $('.table-pembayaran-semester').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.pembayaran.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
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
                @can('edit_kelola_pembayaran', 'delete_kelola_pembayaran')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    })
</script>
