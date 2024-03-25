@include('neo_feeder')
<script>
    const configData = configNeoFeeder.{{ $type }};

    async function getData() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }
            
            let token = await getToken()

            if (token === null) {
                showAlert('GAGAL GET TOKEN', 'error');
                return false;
            }

            $.LoadingOverlay("show");
            let raw = configData.raw;
            raw.token = token.data.token;

            var settings = {
                "url": url,
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(raw),
            };

            $.ajax(settings).done(function(response) {
                $.LoadingOverlay("hide");
                if (configData.changeFormat) {
                    storeData(changeFormatData(response.data))
                } else {
                    storeData(response.data)
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.LoadingOverlay("hide");
                console.error("AJAX Error:", textStatus, errorThrown);
            });
        }
    }

    function changeFormatData(data) {
        $.LoadingOverlay("show");
        let format = configData.format;
        let newData = [];

        $.each(data, function(key, value) {
            let newFormat = {};

            for (const key in value) {
                newFormat[format[key]] = value[key];
            }

            newData.push(newFormat)
        })
        $.LoadingOverlay("hide");
        return newData;
    }

    function storeData(data, func) {
        $.LoadingOverlay("show");
        $.ajax({
            url: '{{ $urlStoreData }}',
            type: 'POST',
            data: {
                tbl: configData.tbl,
                data
            },
            dataType: 'json',
            success: function(res) {
                showAlert(res.message, 'success')
                table.ajax.reload()

                if (func != undefined) {
                    func(response.data);
                }
                $.LoadingOverlay("hide");
            },
            error: function(err) {
                showAlert(err.responseJSON.message, 'error')
                $.LoadingOverlay("hide");
            }
        })
    }
</script>
