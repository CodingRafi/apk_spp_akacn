<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PetugasRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PetugasController extends Controller
{
    public function store(PetugasRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataRequestUser = [
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->email,
                'password' => Hash::make('000000')
            ];

            if ($request->ttd) {
                $dataRequestUser['ttd'] = $request->ttd->store('ttd');
            }

            $user = User::create($dataRequestUser);

            $user->assignRole('petugas');

            $dataRequest = $request->all();
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->petugas()->create($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'petugas'])->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(PetugasRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $role = getRole();
            $user = User::findOrFail($id);
            $dataRequestUser = [
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->email,
            ];

            if ($request->profile) {
                if ($user->profile) {
                    Storage::delete($user->profile);
                }
                $dataRequestUser['profile'] = $request->profile->store('profile');
            }

            if ($request->ttd) {
                if ($user->ttd) {
                    Storage::delete($user->ttd);
                }
                $dataRequestUser['ttd'] = $request->ttd->store('ttd');
            }

            $user->update($dataRequestUser);

            $removeColumn = ['_token', '_method', 'name', 'email', 'login_key', 'profile', 'ttd'];
            $dataRequest = $request->all();
            if ($role->name != 'admin') {
                $removeColumn[] = 'status';
            } else {
                $dataRequest['status'] = $request->status ? "1" : "0";
            }
            $dataRequest = array_diff_key($dataRequest, array_flip($removeColumn));
            $user->petugas()->update($dataRequest);

            DB::commit();
            if ($role->name == 'admin') {
                return redirect()->route('kelola-users.index', ['role' => 'petugas'])->with('success', 'Berhasil diubah');
            } else {
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
            $data->petugas()->delete();
            $data->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
