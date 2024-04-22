<div class="tab-pane" id="dosen-penguji" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-dosen-penguji" aria-label="table-dosen-penguji" style="width: 100%">
            <thead>
                <tr>
                    <th>Dosen</th>
                    <th>Penguji Ke</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    let tableDosenPenguji;
    $(document).ready(function() {
        tableDosenPenguji = $('.table-dosen-penguji').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('mbkm.get-penguji', ['id' => request('id')]) }}',
            columns: [
                {
                    "data": "dosen"
                },
                {
                    "data": "penguji_ke"
                },
            ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
