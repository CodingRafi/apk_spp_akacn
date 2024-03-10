<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'unique:users,email,'. $id,
            'profile' => 'file|mimes:png,jpg,jpeg|max:1024',
            'ttd' => 'file|mimes:png,jpg,jpeg|max:1024'
        ]);

        DB::beginTransaction();
        try {
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

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
