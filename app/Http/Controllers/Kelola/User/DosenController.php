<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DosenRequest;
use App\Models\Agama;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    public function store(DosenRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->profile) {
                $request['path_profile'] = $request->profile->store('profile');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
                'password' => Hash::make('000000'),
                'profile' => $request->path_profile
            ]);

            $user->assignRole('dosen');

            $dataRequest = $request->all();
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->dosen()->create($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'dosen'])->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(DosenRequest $request, $id){
        DB::beginTransaction();
        try {
            $role = getRole();
            $user = User::findOrFail($id);

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

            $removeColumn = ['_token', '_method', 'name', 'email', 'login_key', 'profile'];
            $dataRequest = $request->all();
            if ($role->name != 'admin') {
                $removeColumn[] = 'tunjangan';
                $removeColumn[] = 'status';
            }else{
                $dataRequest['status'] = $request->status ? "1" : "0";
            }
            $dataRequest = array_diff_key($dataRequest, array_flip($removeColumn));
            $user->dosen()->update($dataRequest);
            
            DB::commit();
            if ($role->name == 'admin') {
                return redirect()->route('kelola-users.index', ['role' => 'dosen'])->with('success', 'Berhasil diubah');
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
            $data->dosen()->delete();
            $data->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
