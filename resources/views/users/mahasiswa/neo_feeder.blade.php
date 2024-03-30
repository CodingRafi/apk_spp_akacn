@include('neo_feeder')

<script>
    let dataMahasiswa = [];
    let riwayatMahasiswa = [];
    let statusGetMahasiswa = false;
    let statusGetRiwayat = false;
    let token = null;

    async function getData() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            token = await getToken();

            if (token === null) {
                showAlert('GAGAL GET TOKEN', 'error');
                $.LoadingOverlay("hide");
                return false;
            }

            getMahasiswa();
            getRiwayat();
        }
    }

    async function getMahasiswa() {
        try {
            let raw = {
                "act": "GetDataLengkapMahasiswaProdi",
                "filter": "",
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
                    dataMahasiswa = dataMahasiswa.concat(response.data);
                } else {
                    statusGetMahasiswa = true;
                    keepRunning = false;
                    storeData();
                }

                loop++;
            }

        } catch (error) {
            $.LoadingOverlay("hide");
            console.error("AJAX Error:", error);
            showAlert('Terjadi kesalahan saat mengambil data', 'error');
        }
    }

    async function getRiwayat() {
        try {
            let raw = {
                "act": "GetListRiwayatPendidikanMahasiswa",
                "filter": "",
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
                    riwayatMahasiswa = riwayatMahasiswa.concat(response.data);
                } else {
                    statusGetRiwayat = true;
                    keepRunning = false;
                    storeData();
                }

                loop++;
            }

        } catch (error) {
            $.LoadingOverlay("hide");
            console.error("AJAX Error:", error);
            showAlert('Terjadi kesalahan saat mengambil data', 'error');
        }
    }

    function prosesData() {
        let result = [];

        dataMahasiswa.forEach(item1 => {
            let filteredRiwayat = riwayatMahasiswa.filter(item2 => item2.id_mahasiswa === item1.id_mahasiswa);

            let combinedObject = {
                ...item1,
                riwayat: filteredRiwayat
            };

            result.push(combinedObject);
        });

        return result;
    }

    function storeData(data, func) {
        if (statusGetMahasiswa && statusGetRiwayat) {
            const dataProcess = prosesData()
            const chunks = chunkArray(dataProcess, 50)
    
            chunks.forEach((chunk, index) => {
                $.ajax({
                    url: '{{ route('kelola-users.neo-feeder.mahasiswa.store') }}',
                    type: 'POST',
                    data: {
                        data: JSON.stringify(chunk)
                    },
                    dataType: 'json',
                    success: function(res) {
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

    }
</script>
