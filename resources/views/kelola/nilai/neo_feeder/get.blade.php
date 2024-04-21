@include('neo_feeder')

<script>
    let dataNilai = [];
    let statusGetNilai = false;

    //? Get NIlai Neo Feeder
    async function getDataNeoFeeder() {
        if (confirm('Apakah anda yakin? semua data akan di update dengan data NEO FEEDER')) {
            $.LoadingOverlay("show");
            if (!url) {
                showAlert('Url tidak ditemukan', 'error');
            }

            dataNilai = [];
            statusGetNilai = false;

            token = await getToken();

            if (token === null) {
                showAlert('GAGAL GET TOKEN', 'error');
                $.LoadingOverlay("hide");
                return false;
            }

            try {
                let raw = {
                    "act": "GetDetailNilaiPerkuliahanKelas",
                    "filter": "angkatan='{{ request('tahun_ajaran_id') }}' and nilai_angka is not null and nilai_huruf is not null",
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
                        dataNilai = dataNilai.concat(response.data);
                    } else {
                        statusGetNilai = true;
                        keepRunning = false;
                        storeData()
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

    function prosesData() {
        let data =  dataNilai.map(row => {
            return {
                id_mahasiswa: row.id_mahasiswa,
                id_matkul: row.id_matkul,
                id_prodi: row.id_prodi,
                angkatan: row.angkatan,
                id_registrasi_mahasiswa: row.id_registrasi_mahasiswa,
                id_semester: row.id_semester,
                nilai_angka: row.nilai_angka,
                nilai_huruf: row.nilai_huruf,
                sks_mata_kuliah: row.sks_mata_kuliah,
            }
        });

        return data
    }

    function storeData(data) {
        if (statusGetNilai) {
            const dataProcess = prosesData();
            const chunks = chunkArray(dataProcess, 50)

            chunks.forEach((chunk, index) => {
                $.ajax({
                    url: '{{ route('kelola-nilai.storeNeoFeeder') }}',
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
