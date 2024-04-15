@include('neo_feeder')

<script>
    let dataMahasiswa = [];
    let riwayatMahasiswa = [];
    let statusGetMahasiswa = false;
    let statusGetRiwayat = false;
    let token = null;
    let id_mahasiswa = null;
    let id_registrasi_mahasiswa = null;

    async function getData() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            dataMahasiswa = [];
            riwayatMahasiswa = [];
            statusGetMahasiswa = false;
            statusGetRiwayat = false;

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
            $.LoadingOverlay("hide");
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

    async function updateData(user_id, data) {
        $.ajax({
            url: '{{ route('kelola-users.neo-feeder.mahasiswa.update', ':user_id') }}'.replace(':user_id',
                user_id),
            type: 'PATCH',
            data: data,
            dataType: 'json'
        })
    }

    async function getDetailMahasiswa(user_id) {
        try {
            const res = await $.ajax({
                url: '{{ route('kelola-users.neo-feeder.mahasiswa.show', ':user_id') }}'.replace(':user_id',
                    user_id),
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    async function sendDataMhsToNeoFeeder(user_id, id_mhs) {
        const getData = await getDetailMahasiswa(user_id)
        let token = await getToken()

        if (token === null) {
            showAlert('GAGAL GET TOKEN', 'error');
            $.LoadingOverlay("hide");
            return false;
        }

        if (!id_mhs) {
            const dataMhs = {
                nama_mahasiswa: getData.data.name,
                jenis_kelamin: (getData.data.mahasiswa.jk ? getData.data.mahasiswa.jk.toUpperCase() :
                    '*'),
                jalan: getData.data.mahasiswa.jalan,
                rt: getData.data.mahasiswa.rt,
                rw: getData.data.mahasiswa.rw,
                dusun: getData.data.mahasiswa.dusun,
                kelurahan: getData.data.mahasiswa.kelurahan,
                kode_pos: getData.data.mahasiswa.kode_pos,
                nisn: getData.data.mahasiswa.nisn,
                nik: getData.data.mahasiswa.nik,
                tempat_lahir: getData.data.mahasiswa.tempat_lahir,
                tanggal_lahir: getData.data.mahasiswa.tgl_lahir,

                //? Ayah
                nama_ayah: getData.data.mahasiswa.nama_ayah,
                tanggal_lahir_ayah: getData.data.mahasiswa.tgl_lahir_ayah,
                nik_ayah: getData.data.mahasiswa.nik_ayah,
                id_jenjang_pendidikan_ayah: getData.data.mahasiswa.jenjang_ayah_id,
                id_pekerjaan_ayah: getData.data.mahasiswa.pekerjaan_ayah_id,
                id_pengahasilan_ayah: getData.data.mahasiswa.penghasilan_ayah_id,
                id_kebutuhan_khusus_ayah: getData.data.mahasiswa.ayah_kebutuhan_khusus,

                //? IBU
                nama_ibu_kandung: getData.data.mahasiswa.nama_ibu,
                tanggal_lahir_ibu: getData.data.mahasiswa.tgl_lahir_ibu,
                nik_ibu: getData.data.mahasiswa.nik_ibu,
                id_jenjang_pendidikan_ibu: getData.data.mahasiswa.jenjang_ibu_id,
                id_pekerjaan_ibu: getData.data.mahasiswa.pekerjaan_ibu_id,
                id_pengahasilan_ibu: getData.data.mahasiswa.penghasilan_ibu_id,
                id_kebutuhan_khusus_ibu: getData.data.mahasiswa.ibu_kebutuhan_khusus,

                //? Wali
                nama_wali: getData.data.mahasiswa.nama_wali,
                tanggal_lahir_wali: getData.data.mahasiswa.tgl_lahir_wali,
                id_jenjang_pendidikan_wali: getData.data.mahasiswa.jenjang_wali_id,
                id_pekerjaan_wali: getData.data.mahasiswa.pekerjaan_wali_id,
                id_pengahasilan_wali: getData.data.mahasiswa.penghasilan_wali_id,

                telepon: getData.data.mahasiswa.telepon,
                handphone: getData.data.mahasiswa.handphone,
                email: getData.data.email,
                penerima_kps: getData.data.mahasiswa.penerima_kps,
                no_kps: getData.data.mahasiswa.no_kps,
                npwp: getData.data.mahasiswa.npwp,
                id_wilayah: getData.data.mahasiswa.wilayah_id,
                id_jenis_tinggal: getData.data.mahasiswa.jenis_tinggal_id,
                id_agama: getData.data.mahasiswa.agama_id,
                id_alat_transportasi: getData.data.mahasiswa.alat_transportasi_id,
                kewarganegaraan: getData.data.mahasiswa.kewarganegaraan_id
            };

            let settings = {
                url: url,
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                data: JSON.stringify({
                    "act": "InsertBiodataMahasiswa",
                    "token": token.data.token,
                    "record": dataMhs
                })
            };

            const response = await $.ajax(settings);

            // ? update id_mahasiswa_neo_feeder
            if (response.error_code == '0') {
                id_mahasiswa = response.data.id_mahasiswa
                updateData(user_id, {
                    neo_feeder_id_mahasiswa: response.data.id_mahasiswa
                })
            } else {
                showAlert(response.error_desc, 'error');
                return false;
            }
        } else {
            id_mahasiswa = id_mhs;
        }

        //? insert riwayat pendidikan
        let dataRiwayat = {
            id_mahasiswa: id_mahasiswa,
            nim: getData.data.login_key,
            id_jenis_daftar: getData.data.mahasiswa.jenis_daftar_id,
            id_jalur_daftar: getData.data.mahasiswa.jalur_masuk_id,
            id_periode_masuk: getData.data.mahasiswa.semester_id,
            tanggal_daftar: getData.data.mahasiswa.tgl_daftar,
            id_perguruan_tinggi: id_pt,
            id_prodi: getData.data.mahasiswa.prodi_id,
            id_pembiayaan: getData.data.mahasiswa.jenis_pembiayaan_id,
            biaya_masuk: getData.data.mahasiswa.biaya_masuk,
        };

        let settingsRiwayat = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertRiwayatPendidikanMahasiswa",
                "token": token.data.token,
                "record": dataRiwayat
            })
        };

        const responseRiwayat = await $.ajax(settingsRiwayat);

        if (responseRiwayat.error_code == "0") {
            id_registrasi_mahasiswa = responseRiwayat.data.id_registrasi_mahasiswa
            updateData(user_id, {
                neo_feeder_id_registrasi_mahasiswa: responseRiwayat.data.id_registrasi_mahasiswa,
                sync_neo_feeder: 1
            })

            showAlert('BERHASIL', 'success');
            table.ajax.reload();
            return true
        }
    }
</script>
