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
            }
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
            format: {}
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            format: {}
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
            format: {}
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
            format: {}
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
            format: {}
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
            format: {}
        },
        dosen: {
            raw: {
                "act": "DetailBiodataDosen",
                "filter": "",
                "order": "",
                "limit": "100",
                "offset": "0"
            },
            tbl: 'dosens',
            changeFormat: false,
            format: {}
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
            format: {}
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            format: {}
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
            format: {}
        }
    }
</script>
