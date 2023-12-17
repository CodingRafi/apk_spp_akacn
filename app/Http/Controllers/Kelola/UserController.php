<?php
namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use DataTables;
use App\Models\{
    User,
    TahunAjaran,
    Prodi,
    Mahasiswa,
    Petugas
};
use DB, PDF;
use App\Exports\UserPembayaranExport;
use Illuminate\Http\Request;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_users', ['only' => ['index', 'data', 'store', 'exportPembayaran', 'printPembayaran']]);
        $this->middleware('permission:add_users', ['only' => ['create', 'store', 'import', 'saveImport']]);
        $this->middleware('permission:edit_users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_users', ['only' => ['destroy']]);
    }

    public function index($role){
        $return = [];
        if ($role == 'mahasiswa') {
            $prodis = DB::table('prodi')->get();
            $tahun_ajarans = DB::table('tahun_ajarans')->get();
            $return = [
                'prodis' => $prodis,
                'tahun_ajarans' => $tahun_ajarans
            ];
        }
        return view('users.index', $return);
    }

    public function data($role){
        $datas = User::select('users.*')
                        ->when($role == 'mahasiswa', function($q) use($role){
                            $q->join('mahasiswas as b', 'users.id', 'b.user_id')
                                ->when(request('prodi'), function($q2){
                                    $q2->where('b.prodi_id', request('prodi'));
                                })
                                ->when(request('tahun_ajaran'), function($q2){
                                    $q2->where('b.tahun_ajaran_id', request('tahun_ajaran'));
                                });
                        })
                        ->role($role)
                        ->get();

        foreach ($datas as $data) {
            $options = '';

            if ($role == 'mahasiswa') {
                $options = $options ."<a href='". route('users.print.pembayaran', ['role' => $role, 'user_id' => $data->id]) ."' class='btn btn-info mx-2' target='_blank'>Report Pembayaran</a>";
            }

            if (auth()->user()->can('edit_users')) {
                $options = $options ."<a href='". route('users.edit', ['role' => $role, 'id' => $data->id]) ."' class='btn btn-warning mx-2'>Edit</a>";
            }
            
            if (auth()->user()->can('delete_users')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`". route('users.destroy', ['role' => $role, 'id' => $data->id]) ."`)'>
                                    Hapus
                                </button>";

            }
            $data->options = $options;
        }

        return DataTables::of($datas)
                            ->addIndexColumn()
                            ->rawColumns(['options'])
                            ->make(true);
    }

    public function create($role){
        $return = [];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::all();
            $return = [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis
            ];
        }

        return view('users.form', $return);
    }

    public function store(Request $request, $role){
        $validate = [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
        ];

        if ($role == 'mahasiswa') {
            $validate += [
                'tahun_ajaran_id' => 'required',
                'nim' => 'required|unique:mahasiswas,nim'
            ];
        }else{
            $validate += [
                'nip' => 'required|unique:petugas,nip',
                'ttd' => 'required|file|max:1024|mimes:png,jpg,jpeg'
            ];
        }

        $request->validate($validate);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('000000')
        ]);

        $user->assignRole($role);

        if ($role == 'mahasiswa') {
            Mahasiswa::create([
                'nim' => $request->nim,
                'user_id' => $user->id,
                'prodi_id' => $request->prodi_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id
            ]);
        }else{
            $ttd = $request->file('ttd')->store('ttd');
            Petugas::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'ttd' => $ttd
            ]);
        }

        return redirect()->route('users.index', ['role' => $role])->with('success', 'Berhasil ditambahkan');
    }

    public function edit($role, $id){
        $data = User::findOrFail($id);
        $return = [
            'data' => $data
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::all();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis
            ];
        }

        return view('users.form', $return);
    }

    public function update(Request $request, $role, $id){
        $validate = [
            'email' => 'required|unique:users,email,' . $id,
            'name' => 'required',
        ];

        $user = User::findOrFail($id);

        if ($role == 'mahasiswa') {
            $validate += [
                'tahun_ajaran_id' => 'required',
                'nim' => 'required|unique:mahasiswas,nim,' . $user->mahasiswa->id
            ];
        }else{
            $validate += [
                'nip' => 'required|unique:petugas,nip,' . $user->petugas->nip,
                'ttd' => 'file|max:1024|mimes:png,jpg,jpeg'
            ];
        }

        if ($role == 'mahasiswa') {
            $mhs = $user->mahasiswa;
            if ($mhs->prodi_id != $request->prodi_id || $mhs->tahun_ajaran_id != $request->tahun_ajaran_id) {
                $pembayarans = DB::table('pembayarans')->where('mhs_id', $id)->count();
                if ($pembayarans > 0) {
                    return redirect()->back()->with('error', 'Maaf tidak bisa diubah karena sudah ada pembayaran');
                }
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($role == 'mahasiswa') {
            $mhs->update([
                'nim' => $request->nim,
                'prodi_id' => $request->prodi_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id
            ]);
        }else{
            $update = ['nip' => $request->nip];
            $pts = $user->petugas;
            if ($request->ttd) {
                Storage::delete($pts->ttd);
                $update['ttd'] = $request->file('ttd')->store('ttd');
            }

            $pts->update($update);
        }
        
        return redirect()->route('users.index', ['role' => $role])->with('success', 'Berhasil diubah');
    }

    public function destroy($role, $id){
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            if ($role == 'mahasiswa') {
                $user->mahasiswa->delete();
            }else{
                $user->petugas->delete();
            }
            $user->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal dihapus');
        }
    }

    public function import($role){
        if ($role != 'mahasiswa') {
            abort(404);
        }

        $tahun_ajarans = TahunAjaran::all();
        $prodis = Prodi::all();

        return view('users.import', compact('tahun_ajarans', 'prodis'));
    }

    public function saveImport(Request $request, $role){
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
            'tahun_ajaran_id' => 'required',
            'prodi_id' => 'required'
        ]);

        try {
            $file = $request->file('file');
            Excel::import(new MahasiswaImport($request->tahun_ajaran_id, $request->prodi_id), $file);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return redirect()->back()->withErrors($errors)->withInput();
        }

        return redirect()->route('users.index', ['role' => $role])->with('success', 'Berhasil diimport');
    }

    public function exportPembayaran($role){
        if ($role != 'mahasiswa') {
            abort(404);
        }
        
        if (!request('prodi') && !request('tahun_ajaran')) {
            return redirect()->back()->with('error', 'Harap pilih filter prodi dan tahun ajaran');
        }
        
        return Excel::download(new UserPembayaranExport($role), 'pembayaran.xlsx');
    }

    public function printPembayaran($role, $user_id){
        if ($role != 'mahasiswa') {
            abort(404);
        }

        $data = User::findOrFail($user_id);
        $semesters = DB::table('semesters as a')
                            ->select('a.nama', 'b.nominal', 'b.publish', 'a.id')
                            ->join('semester_tahun as b', 'a.id', 'b.semester_id')
                            ->where('b.tahun_ajaran_id', $data->mahasiswa->tahun_ajaran_id)
                            ->where('b.prodi_id', $data->mahasiswa->prodi_id)
                            ->get();
            
        $pembayarans = [];
        foreach ($semesters as $semester) {
            $payments = DB::table('pembayarans')
                            ->select('pembayarans.*', 'users.name as nama_verify')
                            ->leftJoin('users', 'users.id', 'pembayarans.verify_id')
                            ->where('pembayarans.status', 'diterima')
                            ->where('pembayarans.mhs_id', $data->id)
                            ->where('pembayarans.semester_id', $semester->id)
                            ->get()
                            ->toArray();
            $total = array_sum(array_column($payments, 'nominal'));
            
            $pembayarans[$semester->id] = [
                'total' => $total,
                'payments' => $payments,
                'semester' => $semester
            ];
        }

        return PDF::loadView('kelola_pembayaran.print', compact('data', 'pembayarans'))->stream('pembayaran.pdf');
    }
}
