@include('neo_feeder')

<script>
    let id_kelas_kuliah;
    let dosen = [];
    let mahasiswa = [];
    let loopDosen = 0;
    let loopMhs = 0;
    let statusMhs = false;
    let statusDosen = false;

    async function getData() {
        try {
            const res = await $.ajax({
                url: '{{ route('rekap-perkuliahan.getData', ['semester_id' => request('semester_id'), 'tahun_matkul_id' => request('tahun_matkul_id')]) }}',
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    async function updateData(data) {
        $.ajax({
            url: '{{ route('rekap-perkuliahan.updateNeoFeeder', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
            type: 'PATCH',
            data: data,
            dataType: 'json'
        })
    }

    async function sendDataToNeoFeeder(idKelasKuliah) {
        $.LoadingOverlay("show");
        const res = await getData();
        let token = await getToken();

        if (token === null) {
            showAlert('GAGAL GET TOKEN', 'error');
            $.LoadingOverlay("hide");
            return false;
        }

        dosen = [];
        mahasiswa = [];
        loopDosen = 0;
        loopMhs = 0;
        statusMhs = false;
        statusDosen = false;

        if (!idKelasKuliah) {
            let response = await sendKelasKuliahToNeoFeeder(token, res)

            if (response.error_code == '0') {
                id_kelas_kuliah = response.data.id_kelas_kuliah
                updateData({
                    id_kelas_kuliah: response.data.id_kelas_kuliah
                })
            } else {
                if (response.error_code == '15') {
                    let raw = {
                        "act": "GetDetailKelasKuliah",
                        "filter": `id_semester='{{ request('semester_id') }}' AND id_matkul='${res.matkul_id}'`,
                        "order": "",
                        "token": token.data.token,
                        "limit": "1",
                        "offset": "0",
                    };

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const responseGetKelasKuliah = await $.ajax(settings);
                    
                    if (responseGetKelasKuliah.error_code == '0') {
                        id_kelas_kuliah = responseGetKelasKuliah.data[0].id_kelas_kuliah
                        updateData({
                            id_kelas_kuliah: responseGetKelasKuliah.data[0].id_kelas_kuliah
                        })
                    }else{
                        showAlert(responseGetKelasKuliah.error_desc, 'error');
                    }
                }else{
                    showAlert(response.error_desc, 'error');
                    return false;
                }
            }
        } else {
            id_kelas_kuliah = idKelasKuliah;
        }

        if(res.dosen.length != 0 || res.mahasiswa.length != 0) {
            sendDosenToNeoFeeder(token, res);
            sendMahasiswaToNeoFeeder(token, res);
        }else{
            $.LoadingOverlay("hide");
            showAlert('Data tidak ada yang berubah', 'success');
        }
    }

    async function ajaxSendDosen(token, dataDosen) {
        let settingsDosen = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertDosenPengajarKelasKuliah",
                "token": token.data.token,
                "record": dataDosen
            })
        };

        loopDosen++;
        return $.ajax(settingsDosen);
    }

    async function sendDosenToNeoFeeder(token, res) {
        for (const e of res.dosen) {  // Use for...of loop to allow await
            let dataDosen = {
                id_registrasi_dosen: e.id_registrasi_dosen,
                id_kelas_kuliah: id_kelas_kuliah,
                sks_substansi_total: e.sks_substansi_total,
                rencana_minggu_pertemuan: e.rencana_tatap_muka,
                realisasi_minggu_pertemuan: e.realisasi_tatap_muka,
                id_jenis_evaluasi: e.jenis_evaluasi_id,
            };

            try {
                let responseDosen = await ajaxSendDosen(token, dataDosen);

                if (responseDosen.error_code === '0') {
                    dosen.push({
                        id_registrasi_dosen: e.id_registrasi_dosen,
                        id_aktivitas_mengajar: responseDosen.data.id_aktivitas_mengajar,
                    });

                    if (loopDosen === dosen.length) {
                        updateData({ dosen });
                        statusDosen = true;

                        if (statusMhs && statusDosen) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                            setTimeout(() => {
                                document.location.reload();
                            }, 300);
                        }
                    }
                } else {
                    let raw = {
                        "act": "GetDosenPengajarKelasKuliah",
                        "filter": `id_registrasi_dosen='${e.id_registrasi_dosen}' AND id_kelas_kuliah='${id_kelas_kuliah}'`,
                        "order": "",
                        "token": token.data.token,
                        "limit": "1",
                        "offset": "0",
                    };

                    let settings = {
                        url: url,
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify(raw)
                    };

                    const responseGetDosen = await $.ajax(settings);
                    
                    if (responseGetDosen.error_code == '0') {
                        let id_aktivitas_mengajar = responseGetDosen.data[0].id_aktivitas_mengajar;
                        dosen.push({
                            id_registrasi_dosen: e.id_registrasi_dosen,
                            id_aktivitas_mengajar: id_aktivitas_mengajar,
                        });
                    }

                    showAlert(responseDosen.error_desc, 'error');
                    updateData({ dosen });
                    statusDosen = true;

                    if (statusMhs && statusDosen) {
                        $.LoadingOverlay("hide");
                        showAlert('Data Berhasil dikirim', 'success');
                        setTimeout(() => {
                            document.location.reload();
                        }, 300);
                    }
                    return false;
                }
            } catch (error) {
                console.error(error);
                showAlert('An error occurred', 'error');
            }
        }
    }


    async function ajaxSendMahasiswa(token, dataMhs) {
        let settingsMhs = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertPesertaKelasKuliah",
                "token": token.data.token,
                "record": dataMhs
            })
        };

        loopMhs++;
        return $.ajax(settingsMhs);
    }

    async function sendMahasiswaToNeoFeeder(token, res) {
        res.mahasiswa.forEach(e => {
            let dataMhs = {
                id_registrasi_mahasiswa: e.id_registrasi_mahasiswa,
                id_kelas_kuliah: id_kelas_kuliah
            };

            ajaxSendMahasiswa(token, dataMhs).then(function(responseMhs) {
                
                if (responseMhs.error_code == '0' || responseMhs.error_code == '119') {
                    mahasiswa.push({
                        mhs_id: e.mhs_id,
                        id_kelas_kuliah: id_kelas_kuliah,
                    })
                    
                    if (loopMhs == mahasiswa.length) {
                        updateData({
                            mahasiswa: mahasiswa
                        })

                        statusMhs = true;
                        if (statusMhs && statusDosen) {
                            $.LoadingOverlay("hide");
                            showAlert('Data Berhasil dikirim', 'success');
                            setTimeout(() => {
                                document.location.reload();
                            }, 300);
                        }
                    }
                } else {
                    showAlert(responseMhs.error_desc, 'error');
                    updateData({
                        mahasiswa: mahasiswa
                    })

                    statusMhs = true;
                    if (statusMhs && statusDosen) {
                        $.LoadingOverlay("hide");
                        showAlert('Data Berhasil dikirim', 'success');
                        setTimeout(() => {
                            document.location.reload();
                        }, 300);
                    }
                    return false;
                }
            });

        });
    }

    async function sendKelasKuliahToNeoFeeder(token, res) {
        //? send kelas kuliah
        const dataKelasKuliah = {
            id_prodi: res.prodi_id,
            id_semester: '{{ request('semester_id') }}',
            id_matkul: res.matkul_id,
            nama_kelas_kuliah: res.nama,
            bahasan: res.bahasan,
            tanggal_mulai_efektif: res.tanggal_mulai_efektif,
            tanggal_akhir_efektif: res.tanggal_akhir_efektif,
        };

        let settings = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertKelasKuliah",
                "token": token.data.token,
                "record": dataKelasKuliah
            })
        };

        return $.ajax(settings);
    }
</script>
