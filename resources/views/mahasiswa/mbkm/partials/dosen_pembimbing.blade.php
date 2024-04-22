<div class="tab-pane active" id="dosen-pembimbing" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-dosen-pembimbing" aria-label="table-dosen-pembimbing" style="width: 100%">
            <thead>
                <tr>
                    <th>Dosen</th>
                    <th>Pembimbing Ke</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    let tableDosenPembimbing;
    $(document).ready(function() {
        tableDosenPembimbing = $('.table-dosen-pembimbing').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('mbkm.get-pembimbing', ['id' => request('id')]) }}',
            columns: [
                {
                    "data": "dosen"
                },
                {
                    "data": "pembimbing_ke"
                },
            ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
