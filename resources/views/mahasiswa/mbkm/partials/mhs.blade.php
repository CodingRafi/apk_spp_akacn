<div class="tab-pane" id="mahasiswa" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-mahasiswa" aria-label="table-mahasiswa" style="width: 100%">
            <thead>
                <tr>
                    <th class="col-md-6">Nama</th>
                    <th class="col-md-6">Peran</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    let tableMhs;
    $(document).ready(function() {
        tableMhs = $('.table-mahasiswa').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('mbkm.get-mhs', ['id' => request('id')]) }}',
            columns: [{
                    "data": "mhs"
                },
                {
                    "data": "peran"
                },
            ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
