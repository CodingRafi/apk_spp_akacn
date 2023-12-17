<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB, Storage, Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(User $user)
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validate = [
            'name' => 'required',
            'email' =>  ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'profile' => 'file|mimes:png,jpg,jpeg|max:2048'
        ];
        
        if (Auth::user()->hasRole('mahasiswa')) {
            $validate['nim'] = 'required';
        }else{
            $validate += [
                'nip' => 'required',
                'ttd' => 'file|max:1024|mimes:png,jpg,jpeg'
            ];
        }

        $update = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        $request->validate($validate);
        if ($request->file('profile')) {
            if ($user->profile) {
                Storage::delete($user->profile);
            }
            $update['profile'] = $request->file('profile')->store('profile');
        }

        $user->update($update);

        if (Auth::user()->hasRole('mahasiswa')) {
            $user->mahasiswa->update([
                'nim' => $request->nim
            ]);
        }else{
            $pts = $user->petugas;
            $update = ['nip' => $request->nip];
            if ($request->ttd) {
                if ($pts->ttd) {
                    Storage::delete($pts->ttd);
                }
                $update['ttd'] = $request->file('ttd')->store('ttd');
            }
            $user->petugas->update($update);
        }

        return redirect()->back()->with('success', 'Berhasil diupdate!');
    }
}
