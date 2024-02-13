<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DosenRequest;
use App\Models\Agama;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function create()
    {
        $agamas = Agama::all();
        $wilayah = Wilayah::all();
        return view('users.dosen.form', compact('agamas', 'wilayah'));
    }

    public function store(DosenRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'login_key' => $request->login_key,
                'password' => Hash::make('000000')
            ]);

            $user->assignRole('dosen');

            $dataRequest = $request->all();
            $dataRequest['user_id'] = $user->id;
            $dataRequest['status'] = $request->status ? "1" : "0";
            $user->dosen()->create($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.dosen.index')->with('success', 'Berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);
        $agamas = Agama::all();
        $wilayah = Wilayah::all();
        return view('users.dosen.form', compact('agamas', 'wilayah', 'data'));
    }

    public function update(DosenRequest $request, $id){
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
            $user->dosen()->update($dataRequest);

            DB::commit();
            return redirect()->route('kelola-users.dosen.index')->with('success', 'Berhasil disimpan');
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
