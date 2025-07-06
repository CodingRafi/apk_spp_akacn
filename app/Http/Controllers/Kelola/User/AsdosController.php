<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsdosRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AsdosController extends Controller
{
    public function store(AsdosRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
                'password' => Hash::make('000000')
            ]);

            $user->assignRole('asisten');

            $user->asisten()->create([
                'user_id' => $user->id,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'jk' => $request->jk,
                'agama_id' => $request->agama_id,
                'status' => $request->status ? "1" : "0",
                'jalan' => $request->jalan,
                'dusun' => $request->dusun,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kode_pos' => $request->kode_pos,
                'kewarganegaraan_id' => $request->kewarganegaraan_id,
                'wilayah_id' => $request->wilayah_id,
                'telepon' => $request->telepon,
                'handphone' => $request->handphone,
                'mampu_handle_kebutuhan_khusus' => $request->mampu_handle_kebutuhan_khusus,
                'mampu_handle_kebutuhan_braille' => $request->mampu_handle_kebutuhan_braille,
                'mampu_handle_kebutuhan_bahasa_isyarat' => $request->mampu_handle_kebutuhan_bahasa_isyarat,
            ]);

            $user->asdos_dosen()->sync($request->dosen_id);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'asisten'])->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(AsdosRequest $request, $id){
        $role = getRole();
        $user = User::findOrFail($id);
        
        if ($role->name == 'admin' && $user->asdos->dosen_id != $request->dosen_id) {
            $cek = DB::table('jadwal')->where('pengajar_id', $id)->count();
            if ($cek > 0) {
                return redirect()->back()->with('error', 'Dosen tidak bisa diubah!');
            }
        }

        DB::beginTransaction();
        try {
            $dataRequestUser = [
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
            ];

            if ($request->profile) {
                if ($user->profile) {
                    Storage::delete($user->profile);
                }
                $dataRequestUser['profile'] = $request->profile->store('profile');
            }

            $user->update($dataRequestUser);

            $dataRequest = $request->all();
            $removeColumn = ['_token', '_method', 'name', 'email', 'login_key', 'path_profile', 'profile', 'dosen_id'];
            $dataRequest = $request->all();
            if ($role->name != 'admin') {
                $removeColumn[] = 'status';
            }else{
                $dataRequest['status'] = $request->status ? "1" : "0";
            }
            $dataRequest = array_diff_key($dataRequest, array_flip($removeColumn));
            $user->asisten()->update($dataRequest);

            $user->asdos_dosen()->sync($request->dosen_id);

            DB::commit();
            if ($role->name == 'admin') {
                return redirect()->route('kelola-users.index', ['role' => 'asisten'])->with('success', 'Berhasil diubah');
            }else{
                return redirect()->back()->with('success', 'Berhasil diubah');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = User::findOrFail($id);
            $data->asisten()->delete();
            $data->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
