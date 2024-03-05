<div class="modal fade" id="addMatkul" tabindex="-1" aria-labelledby="addMatkulLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addMatkulLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p class="mb-0">Maximal <strong>{{ $jatah_sks }}</strong> SKS. Total
                            SKS
                            diambil <strong class="sks_diambil">0</strong></p>
                    </div>
                    <span class="text-warning"><small>*note Total SKS diambil = Matkul yang sudah di pilih dan
                            select</small></span>
                    <div class="mb-0">
                        <label for="tahun_matkul_id" class="form-label">Mata Kuliah</label>
                        <select class="form-select select2" name="tahun_matkul_id[]" id="tahun_matkul_id" multiple
                            style="width: 100%">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                        onclick="submitForm(this.form, this)">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let max_sks = {{ $jatah_sks }};
    let total_sks = 0;

    function getMatkul() {
        $('#tahun_matkul_id').empty().attr('disabled', 'disabled');
        $.ajax({
            type: "GET",
            url: "{{ route('krs.getMatkul', $tahun_semester_id) }}",
            success: function(res) {
                $.each(res.data, function(i, e) {
                    $('#tahun_matkul_id').append(
                        `<option value="${e.id}" data-sks="${e.sks_mata_kuliah}">${e.kode} - ${e.nama} (${e.sks_mata_kuliah} SKS | ${e.dosen})</option>`
                    )
                })
                $('#tahun_matkul_id').removeAttr('disabled');
                getTotalSKS();
            },
            error: function() {
                alert('Gagal get matkul, harap hubungi administrator');
                $('#tahun_matkul_id').removeAttr('disabled');
            }
        })
    }

    function getTotalSKS() {
        $.ajax({
            type: "GET",
            url: "{{ route('krs.getTotalSKS', $tahun_semester_id) }}",
            success: function(res) {
                total_sks = parseInt(res.total);
                $('.sks_diambil').text(res.total);
            },
            error: function() {
                alert('Gagal get total SKS, harap hubungi administrator');
            }
        })
    }

    $('#tahun_matkul_id').on('change', function(e) {
        let sks = total_sks;
        ($(this).val()).forEach(e => {
            sks += parseInt($('#tahun_matkul_id option[value="' + e + '"]').data('sks'));
        });
        $('.sks_diambil').text(sks);

        if (sks > max_sks) {
            $('#addMatkul .alert').removeClass('alert-info').addClass('alert-danger');
        } else {
            $('#addMatkul .alert').removeClass('alert-danger').addClass('alert-info');
        }
    })
</script>
