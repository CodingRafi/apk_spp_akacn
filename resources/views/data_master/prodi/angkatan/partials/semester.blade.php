<div class="tab-pane active" id="semester" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5>Semester</h5>
        @can('add_semester')
            <button type="button" class="btn btn-primary"
                onclick="addForm('{{ route('data-master.prodi.semester.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}', 'Tambah Semester', '#AddSemester', getSemester)">
                Tambah Semester
            </button>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-semester">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jatah SKS</th>
                    <th>Tanggal Mulai Semester</th>
                    <th>Tanggal Selesai Semester</th>
                    @can('delete_semester')
                        <th>Aksi</th>
                    @endcan
                </tr>
            </thead>
        </table>
    </div>
</div>

@can('add_semester')
    <div class="modal fade" id="AddSemester" tabindex="-1" aria-labelledby="AddSemesterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="SemesterLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="semester_id" id="semester_id">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jatah_sks" class="form-label">Jatah SKS</label>
                            <input type="number" class="form-control" id="jatah_sks" name="jatah_sks">
                        </div>
                        <div class="mb-3">
                            <label for="tgl_mulai_krs" class="form-label">Tanggal Mulai Pengisian KRS</label>
                            <input type="date" class="form-control" id="tgl_mulai_krs" name="tgl_mulai_krs">
                        </div>
                        <div class="mb-3">
                            <label for="tgl_akhir_krs" class="form-label">Tanggal Akhir Pengisian KRS</label>
                            <input type="date" class="form-control" id="tgl_akhir_krs" name="tgl_akhir_krs">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<script>
    let tableSemester;

    function getSemester() {
        $('#semester_id').attr('disabled', 'disabled');
        $.ajax({
            type: "GET",
            url: "{{ route('data-master.prodi.semester.index', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
            success: function(res) {
                $('#semester_id').empty().append('<option value="">Pilih Semester</option>')
                $.each(res.data, function(i, e) {
                    $('#semester_id').append(
                        `<option value="${e.id}">${e.nama}</option>`
                    )
                })
                $('#semester_id').removeAttr('disabled');
            },
            error: function() {
                console.log('Gagal get semester');
                $('#semester_id').removeAttr('disabled');
            }
        })
    }

    $(document).ready(function() {
        tableSemester = $('.table-semester').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('data-master.prodi.semester.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "nama"
                },
                {
                    "data": "jatah_sks_semester"
                },
                {
                    "data": "tgl_mulai"
                },
                {
                    "data": "tgl_selesai"
                },
                @can('delete_semester')
                    {
                        "data": "options"
                    }
                @endcan
            ],
            pageLength: 25,
        });
    })
</script>
