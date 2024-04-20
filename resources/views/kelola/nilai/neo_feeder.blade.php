@include('neo_feeder')

<script>
    let countSend = 0;
    let countSuccess = 0;
    let countError = 0;
    let dataSuccess = [];

    async function getData() {
        try {
            const res = await $.ajax({
                url: '{{ route('kelola-nilai.getDataNilai', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'rombel_id' => request('rombel_id'), 'tahun_semester_id' => request('tahun_semester_id'), 'tahun_matkul_id' => request('tahun_matkul_id')]) }}',
                type: 'GET',
                dataType: 'json'
            });

            return res;
        } catch (err) {
            throw err;
        }
    }

    function updateData(){
        $.ajax({
            url: '{{ route('kelola-nilai.updateNeoFeeder', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'rombel_id' => request('rombel_id'), 'tahun_semester_id' => request('tahun_semester_id'), 'tahun_matkul_id' => request('tahun_matkul_id')]) }}',
            type: 'PATCH',
            data: dataSuccess,
            dataType: 'json',
            success: function(){
                $.LoadingOverlay("hide");
                showAlert(`Berhasil ${countSuccess}, GAGAL ${countError}`, 'success');
            },
            error: function(){
                $.LoadingOverlay("hide");
                showAlert(`Berhasil ${countSuccess}, GAGAL ${countError}`, 'success');
            }
        })
    }

    async function ajaxSend(token, data) {
        let dataReq = data;
        delete dataReq.mhs_id;

        $.ajax({
            url: url,
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "act": "InsertNilaiTransferPendidikanMahasiswa",
                "token": token.data.token,
                "record": dataReq
            }),
            success: function(res) {
                countSuccess++;

                dataSuccess.push({
                    id_transfer_neo_feeder: res.data.id_transfer,
                    mhs_id: data.mhs_id
                });

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
            return {
                id_registrasi_mahasiswa: e.neo_feeder_id_registrasi_mahasiswa,
                id_matkul: e.matkul_id,
                kode_mata_kuliah_asal: e.kode,
                nama_mata_kuliah_asal: e.matkul,
                sks_mata_kuliah_asal: e.sks_mata_kuliah,
                sks_mata_kuliah_diakui: e.sks_mata_kuliah,
                nilai_huruf_asal: e.huruf,
                nilai_huruf_diakui: e.huruf,
                nilai_angka_diakui: e.nilai,
                mhs_id: e.mhs_id
            }
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
