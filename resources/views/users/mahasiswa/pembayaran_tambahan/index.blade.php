<button class="btn btn-primary mb-3" type="button"
    onclick="addForm('{{ route('kelola-users.pembayaran-tambahan.store', ['user_id' => $mhs_id, 'role' => 'mahasiswa']) }}', 'Tambah Pembayaran Tambahan', '#pembayaranTambahan', addPembayaranTambahan)">Tambah</button>
<div class="table-responsive">
    <table class="table table-pembayaran-tambahan">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Untuk</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="pembayaranTambahan" tabindex="-1" aria-labelledby="pembayaranTambahanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form action="" method="get">
            @method('post')
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="pembayaranTambahanLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type_pembayaran_tambahan" class="form-label">Type</label>
                        <select class="form-select" name="type" id="type_pembayaran_tambahan">
                            <option value="">Pilih Type</option>
                            <option value="semester">Semester</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="div-untuk"></div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input class="form-control" type="text" id="nama" name="nama" />
                    </div>
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal</label>
                        <input class="form-control" type="number" id="nominal" name="nominal" min="0"
                            step="0.01" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                        onclick="submitForm(this.form, this, () => {tablePembayaranTambahan.ajax.reload();tablePembayaran.ajax.reload()})">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="select-semester">
    <div class="mb-3">
        <label for="semester_pembayaran_tambahan" class="form-label">Semester</label>
        <select class="form-select" name="tahun_semester_id" id="semester_pembayaran_tambahan">
            <option value="">Pilih Semester</option>
            @foreach ($tahun_semester as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
            @endforeach
        </select>
    </div>
</template>

<template id="select-lainnya">
    <div class="mb-3">
        <label for="pembayaran_lain_tambahan" class="form-label">Pembayaran</label>
        <select class="form-select" name="tahun_pembayaran_lain_id" id="pembayaran_lain_tambahan">
            <option value="">Pilih Pembayaran</option>
            @foreach ($tahunPembayaranLain as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
            @endforeach
        </select>
    </div>
</template>

<script>
    $('#type_pembayaran_tambahan').on('change', function() {
        $('.div-untuk').empty();
        if ($(this).val() == 'semester') {
            $('.div-untuk').append($('#select-semester').html());
        } else if ($(this).val() == 'lainnya') {
            $('.div-untuk').append($('#select-lainnya').html());
        }
    })

    function addPembayaranTambahan(){
        $('.div-untuk').empty()
    }

    function editPembayaranTambahan(data){
        $('.div-untuk').empty()
        if (data.type == 'semester') {
            $('.div-untuk').append($('#select-semester').html());
            $('#semester_pembayaran_tambahan').val(data.tahun_semester_id)
        }else{
            $('.div-untuk').append($('#select-lainnya').html());
            $('#pembayaran_lain_tambahan').val(data.tahun_pembayaran_lain_id)
        }
    }

    let tablePembayaranTambahan;

    $(document).ready(function() {
        tablePembayaranTambahan = $('.table-pembayaran-tambahan').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('kelola-users.pembayaran-tambahan.data', ['user_id' => $mhs_id, 'role' => 'mahasiswa']) }}',
            },
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "nama"
                },
                {
                    "data": "namaParse"
                },
                {
                    "data": "nominal"
                },
                {
                    "data": "options"
                }
            ],
            pageLength: 25,
        });
    })
</script>
