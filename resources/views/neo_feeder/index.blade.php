@include('neo_feeder')
@php
    $type = isset($type) ? $type : request('type');
@endphp
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
        const format = configData.format;
        const newData = data.map(value => {
            const parseUniq = {};
            const parseValue = {};

            configData.unique.forEach(i => {
                parseUniq[format[i]] = value[i];
            });

            Object.entries(value).forEach(([key, val]) => {
                if (!configData.unique.includes(key)) {
                    parseValue[format[key]] = val;
                }
            });

            return [parseUniq, parseValue];
        });

        $.LoadingOverlay("hide");
        return newData;
    }

    function chunkArray(arr, chunkSize) {
        if (arr.length <= chunkSize) {
            return [arr];
        } else {
            return [arr.slice(0, chunkSize), ...chunkArray(arr.slice(chunkSize), chunkSize)];
        }
    }

    function storeData(data, func) {
        const chunks = chunkArray(data, 30)
        let loop = 0;

        chunks.forEach((chunk, index) => {
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
                    showAlert(`Batch ${index+1} Berhasil disimpan`, 'success')

                    if (typeof table !== "undefined") {
                        table.ajax.reload();
                    } else {
                        fetchDataAndUpdateTable()
                    }

                    if (func != undefined) {
                        func(response.data);
                    }
                    
                    loop += 1;
                    if (loop == chunks.length) {
                        $.LoadingOverlay("hide");
                    }
                },
                error: function(err) {
                    showAlert(err.responseJSON.message, 'error')
                    $.LoadingOverlay("hide");
                }
            })
        })
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    let columns = [];

    $(document).ready(function() {
        if (typeof table !== "undefined") {
            table.ajax.reload();
        } else {
            $('.table thead tr').empty();

            const format = configData.format;
            const uniq = configData.unique;

            for (let key in format) {
                if (!uniq.includes(key)) {
                    columns.push({
                        data: format[key],
                        title: capitalize(format[key].replace(/_/g, ' ')),
                    });
                }
            }

            for (const i in columns) {
                $('.table thead tr').append(`<th>${columns[i].title}</th>`);
            }
            fetchDataAndUpdateTable()
        }
    })

    function fetchDataAndUpdateTable() {
        fetch('{{ route('neo-feeder.data', ['type' => $type]) }}')
            .then(response => response.json())
            .then(data => {
                $('.table').DataTable().destroy();

                $('.table').DataTable({
                    columns: columns,
                    data: data,
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }
</script>
