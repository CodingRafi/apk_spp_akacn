@include('neo_feeder')

<script>
    let countSend = 0;
    let countSuccess = 0;
    let countError = 0;
    let dataSuccess = [];

    //? Insert Neo Feeder
    async function getData() {
        try {
            const res = await $.ajax({
                url: '{{ route('kelola-nilai.getDataNilai', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    function updateData() {
        $.ajax({
            url: '{{ route('kelola-nilai.updateNeoFeeder', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
            type: 'PATCH',
            data: {
                data: JSON.stringify(dataSuccess)
            },
            dataType: 'json',
            success: function() {
                $.LoadingOverlay("hide");
                showAlert(`Berhasil ${countSuccess}, GAGAL ${countError}`, 'success');
            },
            error: function() {
                $.LoadingOverlay("hide");
                showAlert(`Berhasil ${countSuccess}, GAGAL ${countError}`, 'error');
            }
        })
    }

    async function ajaxSend(token, data) {

        $.ajax({
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "UpdateNilaiPerkuliahanKelas",
                "token": token.data.token,
                "key" : {
                    "id_registrasi_mahasiswa" : data[0].id_registrasi_mahasiswa,
                    "id_kelas_kuliah": data[0].id_kelas_kuliah
                },
                "record": data[1]
            }),
            success: function(res) {
                console.log(res)
                if (res.error_code == '0') {
                    countSuccess++;
    
                    dataSuccess.push({
                        mhs_id: data[0].mhs_id,
                        tahun_matkul_id: data[0].tahun_matkul_id,
                    });
                }else{
                    countError++;
                }

                if (countSend === (countSuccess + countError)) {
                    updateData()
                }
            },
            error: function(err) {
                countError++;

                if (countSend === (countSuccess + countError)) {
                    updateData()
                }
            }
        });

        countSend++;
    }

    async function sendNeoFeeder() {
        $.LoadingOverlay("show");
        countError, countSuccess, countSend = 0;

        const res = await getData();
        const data = res.map((e) => {
            return [
                {
                    id_registrasi_mahasiswa: e.neo_feeder_id_registrasi_mahasiswa,
                    id_kelas_kuliah: e.id_kelas_kuliah_neo_feeder,
                    mhs_id: e.mhs_id,
                    tahun_matkul_id: e.tahun_matkul_id,
                },
                {
                    nilai_angka: e.nilai_angka,
                    nilai_indeks: e.nilai_indeks,
                    nilai_huruf: e.nilai_huruf,
                    nilai_aktivitas_partisipatif: e.nilai_aktivitas_partisipatif,
                    nilai_hasil_proyek: e.nilai_hasil_proyek,
                    nilai_quiz: e.nilai_quiz,
                    nilai_tugas: e.nilai_tugas,
                    nilai_ujian_tengah_semester: e.nilai_ujian_tengah_semester,
                    nilai_ujian_akhir_semester: e.nilai_ujian_akhir_semester
                }
            ];
        })

        const token = await getToken()

        if (token === null) {
            showAlert('GAGAL GET TOKEN', 'error');
            $.LoadingOverlay("hide");
            return false;
        }

        data.forEach(e => {
            ajaxSend(token, e);
        });
    }
</script>
