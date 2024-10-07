@include('neo_feeder')
@php
    $type = isset($type) ? $type : request('type');
@endphp
<script>
    const configData = configNeoFeeder.{{ $type }};
    let limitGet = 500;
    let process = 0;
    let processDone = 0;

    async function nonActive() {
        return $.ajax({
            url: '{{ route('neo-feeder.nonActive', ['type' => ":type"]) }}'.replace(':type', configData.tbl),
            type: 'PATCH',
        })
    }

    async function getData(rawParams = null, func) {
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

            process = 0;
            processDone = 0;
            
            if (configData.with_is_active == null || configData.with_is_active == true){
                //? Nonactive all data
                await nonActive();
            }
            
            try {
                let raw = rawParams ?? configData.raw;
                raw.token = token.data.token;

                let keepRunning = true;

                while (keepRunning) {
                    showAlert(`Loop ${process + 1} sedang berjalan`, 'success');

                    raw.limit = limitGet;
                    raw.offset = process * limitGet;

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
                        process++;
                        if (configData.changeFormat) {
                            storeData(changeFormatData(response.data));
                        } else {
                            storeData(response.data, func);
                        }
                    }else{
                        keepRunning = false;
                    }
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
                    processDone++;
                    if (process == processDone) {
                        if (typeof thisPage != 'undefined' && thisPage == 'neo_feeder') {
                            if (typeof table !== "undefined") {
                                table.ajax.reload();
                            } else {
                                fetchDataAndUpdateTable()
                            }
                        }
                        showAlert('Berhasil di get!', 'success');
                    }

                    if (func != undefined) {
                        func(res.data);
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

    function fetchDataAndUpdateTable() {
        fetch('{{ route('neo-feeder.data', ['type' => ":type"]) }}'.replace(':type', configData.tbl))
            .then(response => response.json())
            .then(data => {
                $('.table').DataTable().destroy();

                data = data.map(item => {
                    if (item.active) {
                        item.active = "<i class='bx bx-check text-success'></i>"
                    }else{
                        item.active = "<i class='bx bx-x text-danger'></i>"
                    }

                    return item
                })

                $('.table').DataTable({
                    columns: columns,
                    data: data,
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }
</script>
