<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\MBKM;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MBKMNeoFeederController extends Controller
{
    public function store(Request $request, $prodi_id, $tahun_ajaran_id)
    {
        $dataReq = json_decode($request->data);
        DB::beginTransaction();
        try {
            foreach ($dataReq as $data) {
                $tahunSemester = DB::table('tahun_semester')
                    ->where('prodi_id', $prodi_id)
                    ->where('tahun_ajaran_id', $tahun_ajaran_id)
                    ->where('semester_id', $data->id_semester)
                    ->first();

                DB::table('mbkm')
                    ->updateOrInsert([
                        'id_neo_feeder' => $data->id_aktivitas,
                    ], [
                        'jenis_anggota' => $data->jenis_anggota,
                        'jenis_aktivitas_id' => $data->id_jenis_aktivitas,
                        'tahun_semester_id' => $tahunSemester->id,
                        'judul' => $data->judul,
                        'ket' => $data->keterangan,
                        'lokasi' => $data->lokasi,
                        'sk_tugas' => $data->sk_tugas,
                        'tgl_sk_tugas' => Carbon::parse($data->tanggal_sk_tugas)->format('Y-m-d'),
                        'tanggal_mulai' => Carbon::parse($data->tanggal_mulai)->format('Y-m-d'),
                        'tanggal_selesai' => Carbon::parse($data->tanggal_selesai)->format('Y-m-d'),
                    ]);

                $get = DB::table('mbkm')->where('id_neo_feeder', $data->id_aktivitas)->first();

                //? Mahasiswa
                foreach ($data->mhs as $mhs) {
                    $userMhs = DB::table('users')
                        ->select('users.id')
                        ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                        ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $mhs->id_registrasi_mahasiswa)
                        ->first();

                    DB::table('mbkm_mhs')->updateOrInsert([
                        'id_anggota_neo_feeder' => $mhs->id_anggota,
                        'mbkm_id' => $get->id,
                        'mhs_id' => $userMhs->id,
                    ], [
                        'sync' => 1,
                        'peran' => $mhs->jenis_peran
                    ]);
                }

                //? Dosen Pembimbing
                foreach ($data->dosen_pembimbing as $dosenPembimbing) {
                    $userDosen = DB::table('users')
                        ->select('users.id')
                        ->where('users.id_neo_feeder', $dosenPembimbing->id_dosen)
                        ->first();

                    DB::table('mbkm_dosen_pembimbing')->updateOrInsert([
                        'id_bimbing_mahasiswa_neo_feeder' => $dosenPembimbing->id_bimbing_mahasiswa,
                        'mbkm_id' => $get->id,
                        'dosen_id' => $userDosen->id,
                    ], [
                        'kategori_kegiatan_id' => $dosenPembimbing->id_kategori_kegiatan,
                        'pembimbing_ke' => $dosenPembimbing->pembimbing_ke,
                    ]);
                }

                //? Dosen Penguji
                foreach ($data->dosen_penguji as $dosenPenguji) {
                    $userDosen = DB::table('users')
                        ->select('users.id')
                        ->where('users.id_neo_feeder', $dosenPenguji->id_dosen)
                        ->first();

                    DB::table('mbkm_dosen_penguji')->updateOrInsert([
                        'id_penguji_mahasiswa_neo_feeder' => $dosenPenguji->id_uji,
                        'mbkm_id' => $get->id,
                        'dosen_id' => $userDosen->id,
                    ], [
                        'kategori_kegiatan_id' => $dosenPembimbing->id_kategori_kegiatan,
                        'penguji_ke' => $dosenPenguji->penguji_ke,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Berhasil simpan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $data = DB::table('mbkm')
            ->select('mbkm.*', 'tahun_semester.semester_id')
            ->join('tahun_semester', 'tahun_semester.id', 'mbkm.tahun_semester_id')
            ->where('mbkm.id', $mbkm_id)
            ->first();

        $data->mahasiswa = DB::table('mbkm_mhs')
            ->select(
                'profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa',
                'mbkm_mhs.peran'
            )
            ->join('users', 'users.id', '=', 'mbkm_mhs.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->where('mbkm_mhs.mbkm_id', $mbkm_id)
            ->whereNull('mbkm_mhs.id_anggota_neo_feeder')
            ->get();

        $data->dosenPembimbing = DB::table('mbkm_dosen_pembimbing')
            ->select(
                'mbkm_dosen_pembimbing.kategori_kegiatan_id',
                'mbkm_dosen_pembimbing.pembimbing_ke',
                'users.id_neo_feeder'
            )
            ->join('users', 'users.id', '=', 'mbkm_dosen_pembimbing.dosen_id')
            ->where('mbkm_dosen_pembimbing.mbkm_id', $mbkm_id)
            ->whereNull('mbkm_dosen_pembimbing.id_bimbing_mahasiswa_neo_feeder')
            ->get();

        $data->dosenPenguji = DB::table('mbkm_dosen_penguji')
            ->select(
                'mbkm_dosen_penguji.kategori_kegiatan_id',
                'mbkm_dosen_penguji.penguji_ke',
                'users.id_neo_feeder'
            )
            ->join('users', 'users.id', '=', 'mbkm_dosen_penguji.dosen_id')
            ->where('mbkm_dosen_penguji.mbkm_id', $mbkm_id)
            ->whereNull('mbkm_dosen_penguji.id_uji_neo_feeder')
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        if ($request->id_neo_feeder) {
            DB::table('mbkm')
                ->where('id', $mbkm_id)
                ->update([
                    'id_neo_feeder' => $request->id_neo_feeder
                ]);
        }

        if ($request->mahasiswa && count($request->mahasiswa) > 0) {
            foreach ($request->mahasiswa as $mhs) {
                $user = DB::table('users')
                    ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
                    ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $mhs['id_registrasi_mahasiswa'])
                    ->first();

                DB::table('mbkm_mhs')
                    ->where('mhs_id', $user->id)
                    ->where('mbkm_id', $mbkm_id)
                    ->update([
                        'id_anggota_neo_feeder' => $mhs['id_anggota']
                    ]);
            }
        }

        if ($request->dosenPembimbing && count($request->dosenPembimbing) > 0) {
            foreach ($request->dosenPembimbing as $dosenPembimbing) {
                $user = DB::table('users')
                    ->where('id_neo_feeder', $dosenPembimbing['id_dosen'])
                    ->first();

                DB::table('mbkm_dosen_pembimbing')
                    ->where('dosen_id', $user->id)
                    ->where('mbkm_id', $mbkm_id)
                    ->update([
                        'id_bimbing_mahasiswa_neo_feeder' => $dosenPembimbing['id_bimbing_mahasiswa']
                    ]);
            }
        }

        if ($request->dosenPenguji && count($request->dosenPenguji) > 0) {
            foreach ($request->dosenPenguji as $dosenPenguji) {
                $user = DB::table('users')
                    ->where('id_neo_feeder', $dosenPenguji['id_dosen'])
                    ->first();

                DB::table('mbkm_dosen_penguji')
                    ->where('dosen_id', $user->id)
                    ->where('mbkm_id', $mbkm_id)
                    ->update([
                        'id_uji_neo_feeder' => $dosenPenguji['id_uji']
                    ]);
            }
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
