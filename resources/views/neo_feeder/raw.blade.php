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
            unique: ['id'],
            format: {
                id_agama: 'id',
                nama_agama: 'nama'
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
            format: {
                id_jenis_tinggal: 'id',
                nama_jenis_tinggal: 'nama'
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
            format: {
                id_alat_transportasi: 'id',
                nama_alat_transportasi: 'nama'
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
                "limit": "5000",
                "offset": "0"
            },
            tbl: 'wilayahs',
            changeFormat: true,
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
            changeFormat: true,
            format: {
                id_tahun_ajaran: 'id',
                nama_tahun_ajaran: 'nama',
                a_periode_aktif: 'status',
                tanggal_mulai: 'tgl_mulai',
                tanggal_selesai: 'tgl_selesai',
            }
        },
        //? perlu dicoba semester
        semester: {
            raw: {
                "act": "GetSemester",
                "filter": "",
                "order": "",
                "limit": "1000",
                "offset": "0"
            },
            tbl: 'semesters',
            changeFormat: true,
            format: {
                id_semester: 'id',
                nama_semester: 'nama',
                a_periode_aktif: 'status',
                tanggal_mulai: 'tgl_mulai',
                tanggal_selesai: 'tgl_selesai',
            }
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
            changeFormat: true,
            format: {
                id_prodi: 'id',
                kode_program_studi: 'kode',
                nama_program_studi: 'nama',
                status: 'akreditas',
                id_jenjang_pendidikan: 'jenjang_id'
            }
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
            changeFormat: true,
            format: {
                id_kurikulum: 'id',
                nama_kurikulum: 'nama'
            }
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
            format: {
                id_pembiayaan: 'id',
                nama_pembiayaan: 'nama'
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
            format: {
                id_jenis_keluar: 'id',
                jenis_keluar: 'jenis',
                apa_mahasiswa: 'apa_mahasiswa'
            }
        }
    }
</script>
