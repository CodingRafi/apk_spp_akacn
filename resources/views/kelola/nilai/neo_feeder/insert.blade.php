<script>
    function sendNeoFeeder() {
        $.LoadingOverlay("show");
        $.ajax({
            url: '{{ route('kelola-nilai.storeNeoFeeder', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                showAlert(res.message, 'success');
                table.ajax.reload();
                $.LoadingOverlay("hide");
            },
            error: function(err) {
                showAlert(err.responseJSON.message, 'error');
                $.LoadingOverlay("hide");
            }
        });
    }
</script>
