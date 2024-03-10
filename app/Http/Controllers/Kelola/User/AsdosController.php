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

            $user->assignRole('asdos');

            $dataRequest = $request->all();
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->asdos()->create($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'asdos'])->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(AsdosRequest $request, $id){
        $role = getRole();
        $user = User::findOrFail($id);
        
        if ($role->name == 'admin' && $user->asdos->dosen_id != $request->dosen_id) {
            $cek = DB::table('jadwals')->where('pengajar_id', $id)->count();
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
            $removeColumn = ['_token', '_method', 'name', 'email', 'login_key', 'path_profile', 'profile'];
            $dataRequest = $request->all();
            if ($role->name != 'admin') {
                $removeColumn[] = 'dosen_id';
                $removeColumn[] = 'status';
            }else{
                $dataRequest['status'] = $request->status ? "1" : "0";
            }
            $dataRequest = array_diff_key($dataRequest, array_flip($removeColumn));
            $user->asdos()->update($dataRequest);

            DB::commit();
            if ($role->name == 'admin') {
                return redirect()->route('kelola-users.index', ['role' => 'asdos'])->with('success', 'Berhasil diubah');
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
            $data->asdos()->delete();
            $data->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
