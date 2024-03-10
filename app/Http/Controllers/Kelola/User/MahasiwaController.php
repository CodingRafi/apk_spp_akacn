<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MahasiwaController extends Controller
{
    public function store(MahasiswaRequest $request){
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
                'password' => Hash::make('000000')
            ]);

            $user->assignRole('mahasiswa');

            $dataRequest = $request->all();
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->mahasiswa()->create($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'mahasiswa'])->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(MahasiswaRequest $request, $id){
        DB::beginTransaction();
        try {
            $role = getRole();
            $user = User::findOrFail($id);
            $dataRequestUser = [
                'name' => $request->name,
                'email' => $request->email,
                'profile' => $request->path_profile
            ];

            if ($request->profile) {
                if ($user->profile) {
                    Storage::delete($user->profile);
                }
                $dataRequestUser['profile'] = $request->profile->store('profile');
            }

            if ($role->name == 'admin') {
                $dataRequestUser['login_key'] = $request->login_key;
            }

            $user->update($dataRequestUser);

            $removeColumn = ['_token', '_method', 'name', 'email', 'login_key', 'path_profile', 'profile'];
            $dataRequest = $request->all();
            if ($role->name != 'admin') {
                $removeColumn[] = 'status';
                $removeColumn[] = 'tahun_masuk_id';
                $removeColumn[] = 'prodi_id';
                $removeColumn[] = 'rombel_id';
            }else{
                $dataRequest['status'] = $request->status ? "1" : "0";
            }
            $dataRequest = array_diff_key($dataRequest, array_flip($removeColumn));
            $user->mahasiswa()->update($dataRequest);

            DB::commit();
            if ($role->name == 'admin') {
                return redirect()->route('kelola-users.index', ['role' => 'mahasiswa'])->with('success', 'Berhasil diubah');
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
            $data->mahasiswa()->delete();
            $data->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
