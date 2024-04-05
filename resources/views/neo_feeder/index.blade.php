@include('neo_feeder')
@php
    $type = isset($type) ? $type : request('type');
@endphp
<script>
    const configData = configNeoFeeder.{{ $type }};

    async function getData(rawParams = null) {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            let token = await getToken()

            if (token === null) {
                showAlert('GAGAL GET TOKEN', 'error');
                $.LoadingOverlay("hide");
                return false;
            }

            try {
                let raw = rawParams ?? configData.raw;
                raw.token = token.data.token;

                const limit = 500;
                let loop = 0;
                let keepRunning = true;

                while (keepRunning) {
                    showAlert(`Loop ${loop + 1} sedang berjalan`, 'success');

                    raw.limit = limit;
                    raw.offset = loop * limit;

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const response = await $.ajax(settings);

                    if (response.data.length > 0) {
                        if (configData.changeFormat) {
                            storeData(changeFormatData(response.data));
                        } else {
                            storeData(response.data);
                        }
                    }else{
                        keepRunning = false;
                    }

                    loop++;
                }
                $.LoadingOverlay("hide");
            } catch (error) {
                $.LoadingOverlay("hide");
                console.error("AJAX Error:", error);
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }
    }

    function changeFormatData(data) {
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

        return newData;
    }

    function storeData(data, func) {
        const chunks = chunkArray(data, 50)
        let loop = 0;

        chunks.forEach((chunk, index) => {
            $.ajax({
                url: '{{ $urlStoreData }}',
                type: 'POST',
                data: {
                    tbl: configData.tbl,
                    data: chunk
                },
                dataType: 'json',
                success: function(res) {
                    // if (typeof table !== "undefined") {
                    //     table.ajax.reload();
                    // } else {
                    //     fetchDataAndUpdateTable()
                    // }

                    if (func != undefined) {
                        func(response.data);
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
        // if (typeof table !== "undefined") {
        //     table.ajax.reload();
        // } else {
        //     $('.table thead tr').empty();

        //     const format = configData.format;
        //     const uniq = configData.unique;

        //     for (let key in format) {
        //         if (!uniq.includes(key)) {
        //             columns.push({
        //                 data: format[key],
        //                 title: capitalize(format[key].replace(/_/g, ' ')),
        //             });
        //         }
        //     }

        //     for (const i in columns) {
        //         $('.table thead tr').append(`<th>${columns[i].title}</th>`);
        //     }
        //     // fetchDataAndUpdateTable()
        // }
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
