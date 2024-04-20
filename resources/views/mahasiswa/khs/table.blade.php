@php
    $mhs_id = isset($mhs_id) ? $mhs_id : null;
@endphp

<div class="table-responsive">
    <table class="table w-100 table-khs" aria-label="table-khs">
        <thead>
            <tr>
                <th>No</th>
                <th>Semester</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.table-khs').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('khs.dataSemester', ['mhs_id' => $mhs_id]) }}',
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
