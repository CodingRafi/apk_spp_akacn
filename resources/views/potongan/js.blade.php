@php
    $role = Auth::user()->getRoleNames()[0];
@endphp
<div class="modal fade" id="DetailPotongan" tabindex="-1" aria-labelledby="DetailPotonganLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="DetailPotonganLabel">Detail Potongan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input class="form-control" type="text" id="nama" disabled />
                </div>
                <div class="mb-3">
                    <label for="nominal" class="form-label">Nominal</label>
                    <input class="form-control" type="text" id="nominal" disabled />
                </div>
                @if ($role !== 'mahasiswa')
                <div class="mb-3">
                    <label for="prodi" class="form-label">Prodi</label>
                    <input class="form-control" type="text" id="prodi" disabled />
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <input class="form-control" type="text" id="semester" disabled />
                </div>
                <div class="mb-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <input class="form-control" type="text" id="tahun_ajaran" disabled />
                </div>
                @endif
                <div class="mb-3">
                    <label for="ket" class="form-label">Keterangan</label>
                    <div class="ket"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function detailPotongan(id) {
        $('#DetailPotongan').modal('show');

        $.ajax({
            url: `{{ route('data-master.potongan.show', ':id') }}`.replace(':id', id),
            success: function(res) {
                $('#nama').val(res.data.nama);
                $('#nominal').val(res.data.nominal);
                @if ($role !== 'mahasiswa')
                $('#prodi').val(res.data.prodi.nama);
                $('#semester').val(res.data.semester.nama);
                $('#tahun_ajaran').val(res.data.tahun_ajaran.nama);
                @endif
                $('.ket').html(res.data.ket);
            }
        })
    }
</script>
