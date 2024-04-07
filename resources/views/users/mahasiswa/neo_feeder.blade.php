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

    async function getDetailMahasiswa(user_id) {
        try {
            const res = await $.ajax({
                url: '{{ route('kelola-users.neo-feeder.mahasiswa.show', ':user_id') }}'.replace(':user_id',
                    user_id),
                type: 'GET',
                dataType: 'json'
            });

            const dataMhs = {
                nama_mahasiswa: res.data.name,
                jenis_kelamin: (res.data.mahasiswa.jk ? res.data.mahasiswa.jk.toUpperCase() :
                    '*'),
                jalan: res.data.mahasiswa.jalan,
                rt: res.data.mahasiswa.rt,
                rw: res.data.mahasiswa.rw,
                dusun: res.data.mahasiswa.dusun,
                kelurahan: res.data.mahasiswa.kelurahan,
                kode_pos: res.data.mahasiswa.kode_pos,
                nisn: res.data.mahasiswa.nisn,
                nik: res.data.mahasiswa.nik,
                tempat_lahir: res.data.mahasiswa.tempat_lahir,
                tanggal_lahir: res.data.mahasiswa.tgl_lahir,

                //? Ayah
                nama_ayah: res.data.mahasiswa.nama_ayah,
                tanggal_lahir_ayah: res.data.mahasiswa.tgl_lahir_ayah,
                nik_ayah: res.data.mahasiswa.nik_ayah,
                id_jenjang_pendidikan_ayah: res.data.mahasiswa.jenjang_ayah_id,
                id_pekerjaan_ayah: res.data.mahasiswa.pekerjaan_ayah_id,
                id_pengahasilan_ayah: res.data.mahasiswa.penghasilan_ayah_id,
                id_kebutuhan_khusus_ayah: res.data.mahasiswa.ayah_kebutuhan_khusus,

                //? IBU
                nama_ibu_kandung: res.data.mahasiswa.nama_ibu,
                tanggal_lahir_ibu: res.data.mahasiswa.tgl_lahir_ibu,
                nik_ibu: res.data.mahasiswa.nik_ibu,
                id_jenjang_pendidikan_ibu: res.data.mahasiswa.jenjang_ibu_id,
                id_pekerjaan_ibu: res.data.mahasiswa.pekerjaan_ibu_id,
                id_pengahasilan_ibu: res.data.mahasiswa.penghasilan_ibu_id,
                id_kebutuhan_khusus_ibu: res.data.mahasiswa.ibu_kebutuhan_khusus,

                //? Wali
                nama_wali: res.data.mahasiswa.nama_wali,
                tanggal_lahir_wali: res.data.mahasiswa.tgl_lahir_wali,
                id_jenjang_pendidikan_wali: res.data.mahasiswa.jenjang_wali_id,
                id_pekerjaan_wali: res.data.mahasiswa.pekerjaan_wali_id,
                id_pengahasilan_wali: res.data.mahasiswa.penghasilan_wali_id,

                telepon: res.data.mahasiswa.telepon,
                handphone: res.data.mahasiswa.handphone,
                email: res.data.email,
                penerima_kps: res.data.mahasiswa.penerima_kps,
                no_kps: res.data.mahasiswa.no_kps,
                npwp: res.data.mahasiswa.npwp,
                id_wilayah: res.data.mahasiswa.wilayah_id,
                id_jenis_tinggal: res.data.mahasiswa.jenis_tinggal_id,
                id_agama: res.data.mahasiswa.agama_id,
                id_alat_transportasi: res.data.mahasiswa.alat_transportasi_id,
                kewarganegaraan: res.data.mahasiswa.kewarganegaraan_id
            };

            console.log(dataMhs)
            return dataMhs;
        } catch (err) {
            throw err;
        }
    }

    async function sendDataMhsToNeoFeeder(user_id) {
        const data = await getDetailMahasiswa(user_id)
        let token = await getToken()

        if (token === null) {
            showAlert('GAGAL GET TOKEN', 'error');
            $.LoadingOverlay("hide");
            return false;
        }

        let raw = {
            "act": "InsertBiodataMahasiswa",
            "token": token.data.token,
            "record": data
        };

        let settings = {
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify(raw)
        };

        const response = await $.ajax(settings);
        console.log(response)
    }

    function sendToNeoFeeder(user_id) {
        sendDataMhsToNeoFeeder(user_id)
    }
</script>
