<div class="tab-pane active" id="mahasiswa" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="card-title">Mahasiswa</h5>
        <button type="button" class="btn btn-primary"
            onclick="addForm('{{ route('data-master.prodi.mbkm.mahasiswa.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}', 'Tambah Mahasiswa', '#mahasiswaModal', getMhs)">
            Tambah
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-mahasiswa" aria-label="table-mahasiswa">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="mahasiswaModal" aria-labelledby="mahasiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                @method('post')
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="mahasiswaModalLabel">Tambah Mahasiswa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mhs_id" class="form-label">Mahasiswa</label>
                        <select name="mhs_id" id="mhs_id" class="form-select select2-mahasiswa" style="width: 100%;">
                            <option value="">Pilih Mahasiswa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas</label>
                        <select name="peran" id="peran" class="form-select">
                            <option value="">Pilih Peran</option>
                            @foreach (config('services.peran') as $key => $peran)
                                <option value="{{ $key }}">{{ $peran }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-start px-3">
                    <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this, () => tableMhs.ajax.reload())">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getMhs(data = {}) {
        $('#mhs_id')
            .empty()
            .append(`<option value="">Pilih Mahasiswa</option>`)
            .attr('disabled', 'disabled');

        $.ajax({
            url: '{{ route('data-master.prodi.mbkm.mahasiswa.get-mhs', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}?except=' +
                (data.mhs_id ?? ''),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $.each(res, function(key, value) {
                    $('#mhs_id').append(
                        `<option value="${value.id}">${value.name} (${value.login_key})</option>`
                    );
                });

                if (data.mhs_id) {
                    $('#mhs_id').val(data.mhs_id);
                } else {
                    $('#mhs_id').removeAttr('disabled');
                }
            },
            error: function(err) {
                alert('Gagal get mahasiswa');
            }
        })
    }

    let tableMhs;
    $(document).ready(function() {
        tableMhs = $('.table-mahasiswa').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('data-master.prodi.mbkm.mahasiswa.data', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'id' => request('id')]) }}',
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "mhs"
                },
                {
                    "data": "peran"
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
