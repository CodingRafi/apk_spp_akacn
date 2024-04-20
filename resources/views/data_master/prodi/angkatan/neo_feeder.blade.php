@include('neo_feeder')

<script>
    const tahun_semester_id = "{!! $tahun_semester_id !!}";
    let dataAktivitas = [];
    let dataMhsAktivitas = [];
    let statusGetAktivitas = false;
    let statusGetMhsAktivitas = [];

    async function getData() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            dataAktivitas = [];
            dataMhsAktivitas = [];
            statusGetAktivitas = false;
            statusGetMhsAktivitas = [];

            token = await getToken();

            if (token === null) {
                showAlert('GAGAL GET TOKEN', 'error');
                $.LoadingOverlay("hide");
                return false;
            }

            getAktivitas();
        }
    }

    async function getAktivitas() {
        try {
            let raw = {
                "act": "GetListAktivitasMahasiswa",
                "filter": "id_prodi='{{ request('prodi_id') }}' and " + tahun_semester_id,
                "order": "",
            };
            raw.token = token.data.token;

            const limit = 100;
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
                    dataAktivitas = dataAktivitas.concat(response.data);
                } else {
                    statusGetAktivitas = true;
                    keepRunning = false;
                    getMhsAktivitas();
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

    function parseIdAktivitas() {
        const result = [];
        const slice = chunkArray(dataAktivitas, 20);

        slice.forEach(data => {
            let id_aktivitas = data.map(item => (`id_aktivitas = '${item.id_aktivitas}'`)).join(' or ');
            result.push(id_aktivitas);
        });

        return result;
    }

    async function fetchMhsAktivitas(id, i) {
        try {
            let raw = {
                "act": "GetListAnggotaAktivitasMahasiswa",
                "filter": id,
                "order": "",
            };
            raw.token = token.data.token;

            const limit = 100;
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
                    dataMhsAktivitas = dataMhsAktivitas.concat(response.data);
                } else {
                    statusGetMhsAktivitas[i] = true;
                    keepRunning = false;

                    if (statusGetMhsAktivitas.every(value => value === true)) {
                        storeData()
                    }
                }

                loop++;
            }

        } catch (error) {
            $.LoadingOverlay("hide");
            console.error("AJAX Error:", error);
            showAlert('Terjadi kesalahan saat mengambil data', 'error');
        }
    }

    async function getMhsAktivitas() {
        let id_aktivitas = parseIdAktivitas();

        id_aktivitas.forEach((id, i) => {
            statusGetMhsAktivitas.push(false);
            fetchMhsAktivitas(id, i);
        });
    }

    function prosesData() {
        let result = [];

        dataAktivitas.forEach(item1 => {
            let filteredMhs = dataMhsAktivitas.filter(item2 => item2.id_aktivitas === item1.id_aktivitas);

            let combinedObject = {
                ...item1,
                mhs: filteredMhs
            };

            result.push(combinedObject);
        });

        return result;
    }

    function storeData(data, func) {
        if (statusGetAktivitas && statusGetMhsAktivitas.every(value => value === true)) {
            const dataProcess = prosesData()
            const chunks = chunkArray(dataProcess, 20)

            chunks.forEach((chunk, index) => {
                $.ajax({
                    url: '{{ route('data-master.prodi.mbkm.neo-feeder.store', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                    type: 'POST',
                    data: {
                        data: JSON.stringify(chunk)
                    },
                    dataType: 'json',
                    success: function(res) {
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
    }
</script>
