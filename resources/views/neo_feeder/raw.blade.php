<script>
    let configNeoFeeder = {
        agama: {
            raw: {
                "act": "GetAgama",
                "filter": "",
                "order": "",
                "limit": "10",
                "offset": "0"
            },
            tbl: 'agamas',
            changeFormat: true,
            unique: ['id_agama'],
            format: {
                id_agama: 'id',
                nama_agama: 'nama'
            },
            with_is_active: true
        },
        mutu: {
            raw: {
                "act": "GetListSkalaNilaiProdi",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'mutus',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        kategori_kegiatan: {
            raw: {
                "act": "GetKategoriKegiatan",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'kategori_kegiatans',
            changeFormat: true,
            unique: ['id_kategori_kegiatan'],
            format: {
                id_kategori_kegiatan: 'id',
                nama_kategori_kegiatan: 'nama'
            },
            with_is_active: true
        },
        jenis_tinggal: {
            raw: {
                "act": "GetJenisTinggal",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'jenis_tinggals',
            changeFormat: true,
            unique: ['id_jenis_tinggal'],
            format: {
                id_jenis_tinggal: 'id',
                nama_jenis_tinggal: 'nama'
            },
            with_is_active: true
        },
        jenis_evaluasi: {
            raw: {
                "act": "GetJenisEvaluasi",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'jenis_evaluasis',
            changeFormat: true,
            unique: ['id_jenis_evaluasi'],
            format: {
                id_jenis_evaluasi: 'id',
                nama_jenis_evaluasi: 'nama'
            },
            with_is_active: true
        },
        alat_transportasi: {
            raw: {
                "act": "GetAlatTransportasi",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'alat_transportasis',
            changeFormat: true,
            unique: ['id_alat_transportasi'],
            format: {
                id_alat_transportasi: 'id',
                nama_alat_transportasi: 'nama'
            },
            with_is_active: true
        },
        jenis_aktivitas: {
            raw: {
                "act": "GetJenisAktivitasMahasiswa",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'jenis_aktivitas',
            changeFormat: true,
            unique: ['id_jenis_aktivitas_mahasiswa'],
            format: {
                id_jenis_aktivitas_mahasiswa: 'id',
                nama_jenis_aktivitas_mahasiswa: 'nama',
                untuk_kampus_merdeka: 'untuk_kampus_merdeka'
            },
            with_is_active: true
        },
        jenjang: {
            raw: {
                "act": "GetJenjangPendidikan",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'jenjangs',
            changeFormat: true,
            unique: ['id_jenjang_didik'],
            format: {
                id_jenjang_didik: 'id',
                nama_jenjang_didik: 'nama'
            },
            with_is_active: true
        },
        kewarganegaraan: {
            raw: {
                "act": "GetNegara",
                "filter": "",
                "order": "",
                "limit": "500",
                "offset": "0"
            },
            tbl: 'kewarganegaraans',
            changeFormat: true,
            unique: ['id_negara'],
            format: {
                id_negara: 'id',
                nama_negara: 'nama'
            },
            with_is_active: true
        },
        lembaga_pengangkat: {
            raw: {
                "act": "GetLembagaPengangkat",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'lembaga_pengangkats',
            unique: ['id_lembaga_angkat'],
            changeFormat: true,
            format: {
                id_lembaga_angkat: 'id',
                nama_lembaga_angkat: 'nama'
            },
            with_is_active: true
        },
        pekerjaan: {
            raw: {
                "act": "GetPekerjaan",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'pekerjaans',
            unique: ['id_pekerjaan'],
            changeFormat: true,
            format: {
                id_pekerjaan: 'id',
                nama_pekerjaan: 'nama'
            },
            with_is_active: true
        },
        penghasilan: {
            raw: {
                "act": "GetPenghasilan",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'penghasilans',
            unique: ['id_penghasilan'],
            changeFormat: true,
            format: {
                id_penghasilan: 'id',
                nama_penghasilan: 'nama'
            },
            with_is_active: true
        },
        pangkat_golongan: {
            raw: {
                "act": "GetPangkatGolongan",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'pangkat_golongans',
            changeFormat: true,
            unique: ['id_pangkat_golongan'],
            format: {
                id_pangkat_golongan: 'id',
                nama_pangkat: 'nama',
                kode_golongan: 'kode'
            },
            with_is_active: true
        },
        wilayah: {
            raw: {
                "act": "GetWilayah",
                "filter": "",
                "order": "",
            },
            tbl: 'wilayahs',
            changeFormat: true,
            unique: ['id_wilayah'],
            format: {
                id_wilayah: 'id',
                nama_wilayah: 'nama',
                id_level_wilayah: 'id_level_wilayah',
                id_negara: 'negara_id',
                id_induk_wilayah: 'id_induk_wilayah'
            },
            with_is_active: false
        },
        tahun_ajaran: {
            raw: {
                "act": "GetTahunAjaran",
                "filter": "",
                "order": "",
                "limit": "1000",
                "offset": "0"
            },
            tbl: 'tahun_ajarans',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        semester: {
            raw: {
                "act": "GetSemester",
                "filter": "id_tahun_ajaran='{{ request('tahun_ajaran') }}'",
                "order": "",
                "limit": "1000",
                "offset": "0"
            },
            tbl: 'semesters',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        prodi: {
            raw: {
                "act": "GetProdi",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'prodi',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        kurikulum: {
            raw: {
                "act": "GetListKurikulum",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'kurikulums',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        kurikulum_matkul: {
            raw: {
                "act": "GetMatkulKurikulum",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'kurikulum_matkul',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        dosen: {
            raw: {
                "act": "DetailBiodataDosen",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0",
            },
            tbl: 'dosens',
            changeFormat: false,
            format: {},
            with_is_active: false
        },
        mahasiswa: {
            raw: {
                "act": "GetDataLengkapMahasiswaProdi",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'mahasiswas',
            changeFormat: false,
            format: {},
            with_is_active: false
        },
        jenis_pembiayaan: {
            raw: {
                "act": "GetPembiayaan",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'jenis_pembiayaans',
            changeFormat: true,
            unique: ['id_pembiayaan'],
            format: {
                id_pembiayaan: 'id',
                nama_pembiayaan: 'nama'
            },
            with_is_active: true
        },
        status_kepegawaian: {
            raw: {
                "act": "GetStatusKepegawaian",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'status_pegawais',
            changeFormat: true,
            unique: ['id_status_pegawai'],
            format: {
                id_status_pegawai: 'id',
                nama_status_pegawai: 'nama'
            },
            with_is_active: true
        },
        ikatan_kerja: {
            raw: {
                "act": "GetIkatanKerjaSdm",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'ikatan_kerjas',
            changeFormat: true,
            unique: ['id_ikatan_kerja'],
            format: {
                id_ikatan_kerja: 'id',
                nama_ikatan_kerja: 'nama'
            },
            with_is_active: true
        },
        jenis_daftar: {
            raw: {
                "act": "GetJenisPendaftaran",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            unique: ['id_jenis_daftar'],
            tbl: 'jenis_daftars',
            changeFormat: true,
            format: {
                id_jenis_daftar: 'id',
                nama_jenis_daftar: 'nama',
                untuk_daftar_sekolah: 'untuk_daftar_sekolah'
            },
            with_is_active: true
        },
        jalur_masuk: {
            raw: {
                "act": "GetJalurMasuk",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'jalur_masuks',
            unique: ['id_jalur_masuk'],
            changeFormat: true,
            format: {
                id_jalur_masuk: 'id',
                nama_jalur_masuk: 'nama'
            },
            with_is_active: true
        },
        jenis_keluar: {
            raw: {
                "act": "GetJenisKeluar",
                "filter": "",
                "order": "",
                "limit": "50",
                "offset": "0"
            },
            tbl: 'jenis_keluars',
            changeFormat: true,
            unique: ['id_jenis_keluar'],
            format: {
                id_jenis_keluar: 'id',
                jenis_keluar: 'jenis',
                apa_mahasiswa: 'apa_mahasiswa'
            },
            with_is_active: true
        },
        matkul:{
            raw: {
                "act": "GetDetailMataKuliah",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'matkuls',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: false
        },
        penugasan_dosen:{
            raw: {
                "act": "GetListPenugasanDosen",
                "filter": "id_tahun_ajaran = '{{ request('tahun_ajaran_id') }}' and id_perguruan_tinggi = '{{ config('services.neo_feeder.ID_PT') }}'",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'penugasan_dosens',
            changeFormat: false,
            unique: [],
            format: {},
            with_is_active: true
        }
    }
</script>
