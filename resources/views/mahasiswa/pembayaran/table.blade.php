@php
    $mhs_id = isset($mhs_id) ? $mhs_id : null;
@endphp

<div class="table-responsive">
    <table class="table table-pembayaran w-100">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tagihan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    let tablePembayaran;
    $(document).ready(function() {
        tablePembayaran = $('.table-pembayaran').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('pembayaran.data', ['mhs_id' => $mhs_id]) }}',
            columns: [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "nama"
                },
                {
                    "data": "tagihan"
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