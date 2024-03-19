@php
    $mhs_id = isset($mhs_id) ? $mhs_id : null;
@endphp

<div class="table-responsive">
    <table class="table w-100 table-krs">
        <thead>
            <tr>
                <th>No</th>
                <th>Semester</th>
                <th>Jatah SKS</th>
                <th>SKS diambil</th>
                <th>Batas Isi KRS</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.table-krs').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('krs.dataSemester', ['mhs_id' => $mhs_id]) }}',
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "nama"
                },
                {
                    "data": "jatah_sks"
                },
                {
                    "data": "sks_diambil"
                },
                {
                    "data": "tgl_pengisian"
                },
                {
                    "data": "status"
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