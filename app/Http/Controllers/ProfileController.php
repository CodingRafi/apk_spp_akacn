<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use App\Models\AlatTransportasi;
use App\Models\JenisTinggal;
use App\Models\Jenjang;
use App\Models\Kewarganegaraan;
use App\Models\Pekerjaan;
use App\Models\Penghasilan;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $role = getRole()->name;
        $data = Auth::user();
        $agamas = Agama::all();
        $wilayah = Wilayah::all();
        $return = [
            'agamas' => $agamas,
            'wilayah' => $wilayah,
            'data' => $data
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where('status', '1')->get();
            $kewarganegaraan = Kewarganegaraan::all();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans
            ];
        } elseif ($role == 'asdos') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();
            $return += [
                'dosen' => $dosen
            ];
        }

        return view('profile.index', $return);
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
