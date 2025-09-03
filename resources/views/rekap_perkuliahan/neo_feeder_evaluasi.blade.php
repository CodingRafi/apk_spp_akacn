<script>
    function getDataEvaluasi(id_kelas_kuliah) {
        $.LoadingOverlay("show");
        $.ajax({
            url: '{{ route('rekap-perkuliahan.evaluasi.index', ['id_kelas_kuliah' => ':id_kelas_kuliah']) }}'
                .replace(':id_kelas_kuliah', id_kelas_kuliah),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                showAlert(res.message, 'success');
                tableEvaluasi.ajax.reload();
                $.LoadingOverlay("hide");
            },
            error: function(err) {
                showAlert(err.responseJSON.message, 'error');
                $.LoadingOverlay("hide");
            }
        });
    }

    const tableEvaluasi = $('.table-evaluasi').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('rekap-perkuliahan.evaluasi.data', ['id_kelas_kuliah' => $tahunMatkul->id_kelas_kuliah]) }}",
        columns: [{
                "data": "nm_jns_eval"
            },
            {
                "data": "komponen_evaluasi"
            },
            {
                "data": "nama_inggris"
            },
            {
                "data": "bobot"
            }
        ]
    });
</script>
