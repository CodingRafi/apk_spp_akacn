@php
    $mhs_id = isset($mhs_id) ? $mhs_id : null;
    $disabled = isset($disabled) ? $disabled : false;
@endphp

<div class="table-responsive">
    <table class="table table-bimbingan w-100" aria-label="table-bimbingan">
        <thead>
            <tr>
                <th>No</th>
                <th>Semester</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="bimbingan" tabindex="-1" aria-labelledby="bimbinganLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                @method('post')
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="bimbinganLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" cols="30" rows="10" class="form-control" {{ $disabled ? 'disabled' : '' }}></textarea>
                    </div>
                </div>
                @if (!$disabled)
                <div class="modal-footer justify-content-start px-3">
                    <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    let tableBimbingan;
    $(document).ready(function() {
        tableBimbingan = $('.table-bimbingan').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('bimbingan.data', ['mhs_id' => $mhs_id]) }}',
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "nama"
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
