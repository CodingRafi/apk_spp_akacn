@include('neo_feeder')

<script>
    const tahun_semester_id = '{{ request('tahun_semester_id') }}';
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
                url: '{{ route('data-master.tahun-ajaran.matkul.rekap.getData', ['id' => request('id'), 'matkul_id' => request('matkul_id'), 'tahun_semester_id' => request('tahun_semester_id')]) }}',
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    async function updateData(kelas_kuliah_id, data) {
        $.ajax({
            url: '{{ route('data-master.tahun-ajaran.matkul.rekap.updateNeoFeeder', ['id' => request('id'), 'matkul_id' => request('matkul_id'), 'kelas_kuliah_id' => ':kelas_kuliah_id']) }}'
                .replace(
                    ':kelas_kuliah_id', kelas_kuliah_id),
            type: 'PATCH',
            data: data,
            dataType: 'json'
        })
    }

    async function sendDataToNeoFeeder(idKelasKuliah) {
        $.LoadingOverlay("show");
        const res = await getData();
        let token = await getToken()

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
                updateData(res.kelas_kuliah_id, {
                    id_kelas_kuliah: response.data.id_kelas_kuliah
                })
            } else {
                showAlert(response.error_desc, 'error');
                return false;
            }
        } else {
            id_kelas_kuliah = idKelasKuliah;
        }

        sendDosenToNeoFeeder(token, res);
        sendMahasiswaToNeoFeeder(token, res);
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
        res.dosen.forEach(e => {
            let dataDosen = {
                id_registrasi_dosen: e.id_registrasi_dosen,
                id_kelas_kuliah: id_kelas_kuliah,
                sks_substansi_total: e.sks_substansi_total,
                rencana_minggu_pertemuan: e.rencana_tatap_muka,
                realisasi_minggu_pertemuan: e.realisasi_tatap_muka,
                id_jenis_evaluasi: e.jenis_evaluasi_id,
            };

            ajaxSendDosen(token, dataDosen).then(function(responseDosen) {
                if (responseDosen.error_code == '0') {
                    dosen.push({
                        id_registrasi_dosen: e.id_registrasi_dosen,
                        id_aktivitas_mengajar: responseDosen.data.id_aktivitas_mengajar,
                        tahun_semester_id: tahun_semester_id
                    })

                    if (loopDosen == dosen.length) {
                        updateData(res.kelas_kuliah_id, {
                            dosen: dosen
                        })
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
                    showAlert(responseDosen.error_desc, 'error');
                    updateData(res.kelas_kuliah_id, {
                        dosen: dosen
                    })
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
            });
        });
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
                mahasiswa.push({
                    mhs_id: e.mhs_id,
                    id_kelas_kuliah: id_kelas_kuliah,
                    tahun_semester_id: tahun_semester_id
                })

                if (responseMhs.error_code == '0') {
                    if (loopMhs == mahasiswa.length) {
                        updateData(res.kelas_kuliah_id, {
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
                    updateData(res.kelas_kuliah_id, {
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
            id_semester: res.semester_id,
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
