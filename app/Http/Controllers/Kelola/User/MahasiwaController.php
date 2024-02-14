<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
            ]);

            $dataRequest = $request->all();
            $dataRequest = array_diff_key($dataRequest, array_flip(['_token', '_method', 'name', 'email', 'login_key']));
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->mahasiswa()->update($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.index', ['role' => 'mahasiswa'])->with('success', 'Berhasil diubah');
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
