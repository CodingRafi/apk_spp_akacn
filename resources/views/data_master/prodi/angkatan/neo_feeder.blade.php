@include('neo_feeder')

<script>
    const tahun_semester_id = "{!! $tahun_semester_id !!}";
    let dataAktivitas = [];
    let dataMhsAktivitas = [];
    let dataDosenPembimbingAktivitas = [];
    let dataDosenPengujiAktivitas = [];
    let statusGetAktivitas = false;
    let statusGetMhsAktivitas = [];
    let statusGetDosenPembimbingAktivitas = [];
    let statusGetDosenPengujiAktivitas = [];
    let idAktivitasParse = [];

    async function getData() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            dataAktivitas = [];
            dataMhsAktivitas = [];
            dataDosenPembimbingAktivitas = [];
            dataDosenPengujiAktivitas = [];
            statusGetAktivitas = false;
            statusGetMhsAktivitas = [];
            statusGetDosenPembimbingAktivitas = [];
            statusGetDosenPengujiAktivitas = [];
            idAktivitasParse = [];

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
                    idAktivitasParse = parseIdAktivitas();
                    getMhsAktivitas();
                    getDosenPembimbingAktivitas();
                    getDosenPengujiAktivitas();
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

    //? Start Mahasiswa
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
        idAktivitasParse.forEach((id, i) => {
            statusGetMhsAktivitas.push(false);
            fetchMhsAktivitas(id, i);
        });
    }
    //? End Mahasiswa

    //? Start Dosen Pembimbing
    async function fetchDosenPembimbingAktivitas(id, i) {
        try {
            let raw = {
                "act": "GetListBimbingMahasiswa",
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
                    dataDosenPembimbingAktivitas = dataDosenPembimbingAktivitas.concat(response.data);
                } else {
                    statusGetDosenPembimbingAktivitas[i] = true;
                    keepRunning = false;

                    if (statusGetDosenPembimbingAktivitas.every(value => value === true)) {
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

    async function getDosenPembimbingAktivitas() {
        idAktivitasParse.forEach((id, i) => {
            statusGetDosenPembimbingAktivitas.push(false);
            fetchDosenPembimbingAktivitas(id, i);
        });
    }
    //? End Dosen Pembimbing

    //? Start Dosen Penguji
    async function fetchDosenPengujiAktivitas(id, i) {
        try {
            let raw = {
                "act": "GetListUjiMahasiswa",
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
                    dataDosenPengujiAktivitas = dataDosenPengujiAktivitas.concat(response.data);
                } else {
                    statusGetDosenPengujiAktivitas[i] = true;
                    keepRunning = false;

                    if (statusGetDosenPengujiAktivitas.every(value => value === true)) {
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

    async function getDosenPengujiAktivitas() {
        idAktivitasParse.forEach((id, i) => {
            statusGetDosenPengujiAktivitas.push(false);
            fetchDosenPengujiAktivitas(id, i);
        });
    }
    //? End Dosen Pembimbing

    function prosesData() {
        let result = [];

        dataAktivitas.forEach(item1 => {
            let filteredMhs = dataMhsAktivitas.filter(item2 => item2.id_aktivitas === item1.id_aktivitas);
            let filteredDosenPembimbing = dataDosenPembimbingAktivitas.filter(item2 => item2.id_aktivitas ===
                item1.id_aktivitas);
            let filteredDosenPenguji = dataDosenPengujiAktivitas.filter(item2 => item2.id_aktivitas === item1
                .id_aktivitas);

            let combinedObject = {
                ...item1,
                mhs: filteredMhs,
                dosen_pembimbing: filteredDosenPembimbing,
                dosen_penguji: filteredDosenPenguji
            };

            result.push(combinedObject);
        });

        return result;
    }

    function storeData(data, func) {
        if (statusGetAktivitas &&
            statusGetMhsAktivitas.every(value => value === true) &&
            statusGetDosenPembimbingAktivitas.every(value => value === true) && statusGetDosenPengujiAktivitas.every(
                value => value === true)) {
            const dataProcess = prosesData()
            console.log(dataProcess)
            const chunks = chunkArray(dataProcess, 10)

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
<script>
    let aktivitas_id = null;
    let mahasiswa = [];
    let dosenPembimbing = [];
    let dosenPenguji = [];
    let loopMahasiswa = 0;
    let loopDosenPembimbing = 0;
    let loopDosenPenguji = 0;
    let statusMahasiswa = false;
    let statusDosenPembimbing = false;
    let statusDosenPenguji = false;

    async function getDetailMbkm(mbkm_id) {
        try {
            const res = await $.ajax({
                url: '{{ route('data-master.prodi.mbkm.neo-feeder.show', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'mbkm_id' => ':mbkm_id']) }}'
                    .replace(':mbkm_id',
                        mbkm_id),
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    async function updateData(mbkm_id, data) {
        $.ajax({
            url: '{{ route('data-master.prodi.mbkm.neo-feeder.update', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'mbkm_id' => ':mbkm_id']) }}'
                .replace(
                    ':mbkm_id', mbkm_id),
            type: 'PATCH',
            data: data,
            dataType: 'json'
        })
    }

    async function storeToNeoFeeder(mbkm_id, id_aktivitas) {
        let data = await getDetailMbkm(mbkm_id);
        data = data.data;
        let token = await getToken();

        aktivitas_id = null;
        mahasiswa = [];
        dosenPembimbing = [];
        dosenPenguji = [];
        loopMahasiswa = 0;
        loopDosenPembimbing = 0;
        loopDosenPenguji = 0;
        statusMahasiswa = false;
        statusDosenPembimbing = false;
        statusDosenPenguji = false;

        if (token === null) {
            showAlert('GAGAL GET TOKEN', 'error');
            $.LoadingOverlay("hide");
            return false;
        }

        if (!id_aktivitas) {
            const dataMbkm = {
                jenis_anggota: data.jenis_anggota,
                id_jenis_aktivitas: data.jenis_aktivitas_id,
                id_prodi: '{{ request('prodi_id') }}',
                id_semester: data.semester_id,
                judul: data.judul,
                keterangan: data.ket,
                lokasi: data.lokasi,
                sk_tugas: data.sk_tugas,
                tanggal_sk_tugas: data.tgl_sk_tugas,
                tanggal_mulai: data.tanggal_mulai,
                tanggal_selesai: data.tanggal_selesai
            };

            let settings = {
                url: url,
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                data: JSON.stringify({
                    "act": "InsertAktivitasMahasiswa",
                    "token": token.data.token,
                    "record": dataMbkm
                })
            };

            const response = await $.ajax(settings);

            // ? update id_aktivitas
            if (response.error_code == '0') {
                aktivitas_id = response.data.id_aktivitas
                updateData(data.id, {
                    id_neo_feeder: response.data.id_aktivitas
                })
            } else {
                showAlert(response.error_desc, 'error');
                return false;
            }
        } else {
            aktivitas_id = id_aktivitas;
        }

        sendMhsToNeoFeeder(token, data);
        sendDosenPembimbingToNeoFeeder(token, data);
        sendDosenPengujiToNeoFeeder(token, data);
    }

    // Mahasiswa
    async function ajaxSendMhs(token, dataMhs) {
        let settingsMhs = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertAnggotaAktivitasMahasiswa",
                "token": token.data.token,
                "record": dataMhs
            })
        };

        loopMahasiswa++;
        return $.ajax(settingsMhs);
    }

    async function sendMhsToNeoFeeder(token, data) {
        data.mahasiswa.forEach(e => {
            let dataMhs = {
                id_registrasi_mahasiswa: e.neo_feeder_id_registrasi_mahasiswa,
                jenis_peran: e.peran,
                id_aktivitas: aktivitas_id
            };

            ajaxSendMhs(token, dataMhs).then(function(responseMhs) {
                if (responseMhs.error_code == '0') {
                    mahasiswa.push({
                        id_registrasi_mahasiswa: e.neo_feeder_id_registrasi_mahasiswa,
                        id_anggota: responseMhs.data.id_anggota,
                    })

                    if (loopMahasiswa == mahasiswa.length) {
                        updateData(data.id, {
                            mahasiswa: mahasiswa
                        })
                        statusMahasiswa = true;

                        if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                        }
                    }
                } else {
                    showAlert(responseMhs.error_desc, 'error');
                    updateData(data.id, {
                        mahasiswa: mahasiswa
                    })
                    statusMahasiswa = true;

                    if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                        $.LoadingOverlay("hide");
                        showAlert('Data Berhasil dikirim', 'success');
                    }
                    return false;
                }
            });
        });
    }
    // Mahasiswa

    // Dosen Pembimbing
    async function ajaxSendDosenPembimbing(token, dataDosenPembimbing) {
        let settingsDosenPembimbing = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertBimbingMahasiswa",
                "token": token.data.token,
                "record": dataDosenPembimbing
            })
        };

        loopDosenPembimbing++;
        return $.ajax(settingsDosenPembimbing);
    }

    async function sendDosenPembimbingToNeoFeeder(token, data) {
        data.dosenPembimbing.forEach(e => {
            let dataDosenPembimbing = {
                id_aktivitas: aktivitas_id,
                id_kategori_kegiatan: e.kategori_kegiatan_id,
                id_dosen: e.id_neo_feeder,
                pembimbing_ke: e.pembimbing_ke
            };

            ajaxSendDosenPembimbing(token, dataDosenPembimbing).then(function(responseDosenPembimbing) {
                if (responseDosenPembimbing.error_code == '0') {
                    dosenPembimbing.push({
                        id_dosen: e.id_neo_feeder,
                        id_bimbing_mahasiswa: responseDosenPembimbing.data
                            .id_bimbing_mahasiswa
                    })

                    if (loopDosenPembimbing == dosenPembimbing.length) {
                        updateData(data.id, {
                            dosenPembimbing: dosenPembimbing
                        })
                        statusDosenPembimbing = true;

                        if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                        }
                    }
                } else {
                    showAlert(responseDosenPembimbing.error_desc, 'error');
                    if (loopDosenPembimbing == dosenPembimbing.length) {
                        updateData(data.id, {
                            dosenPembimbing: dosenPembimbing
                        })
                        statusDosenPembimbing = true;

                        if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                        }
                    }
                    return false;
                }
            });
        });
    }
    // Dosen Pembimbing

    // Dosen Penguji
    async function ajaxSendDosenPenguji(token, dataDosenPenguji) {
        let settingsDosenPenguji = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertUjiMahasiswa",
                "token": token.data.token,
                "record": dataDosenPenguji
            })
        };

        loopDosenPenguji++;
        return $.ajax(settingsDosenPenguji);
    }

    async function sendDosenPengujiToNeoFeeder(token, data) {
        data.dosenPenguji.forEach(e => {
            let dataDosenPenguji = {
                id_aktivitas: aktivitas_id,
                id_kategori_kegiatan: e.kategori_kegiatan_id,
                id_dosen: e.id_neo_feeder,
                penguji_ke: e.penguji_ke
            };

            ajaxSendDosenPenguji(token, dataDosenPenguji).then(function(responseDosenPenguji) {
                if (responseDosenPenguji.error_code == '0') {
                    dosenPenguji.push({
                        id_dosen: e.id_neo_feeder,
                        id_uji: responseDosenPenguji.data.id_uji
                    })

                    if (loopDosenPenguji == dosenPenguji.length) {
                        updateData(data.id, {
                            dosenPenguji: dosenPenguji
                        })
                        statusDosenPenguji = true;

                        if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                        }
                    }
                } else {
                    showAlert(responseDosenPenguji.error_desc, 'error');
                    if (loopDosenPenguji == dosenPenguji.length) {
                        updateData(data.id, {
                            dosenPenguji: dosenPenguji
                        })
                        statusDosenPenguji = true;

                        if (statusMahasiswa && statusDosenPembimbing && statusDosenPenguji) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                        }
                    }
                    return false;
                }
            });
        });
    }
    // Dosen Pembimbing
</script>
