<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RombelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_rombel', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_rombel', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_rombel', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_rombel', ['only' => ['destroy']]);
    }

    public function index()
    {
        $prodis = Prodi::where('status', "1")->get();
        return view('data_master.rombel.index', compact('prodis'));
    }

    public function data()
    {
        $datas = Rombel::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.rombel.dosen-pa.index', $data->id) . "' class='btn btn-primary'>Set Dosen PA</a>";

            if (auth()->user()->can('edit_rombel')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.rombel.show', $data->id) . "`, `Edit Rombel`, `#rombel`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_rombel')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'prodi_id' => 'required'
        ]);

        Rombel::create([
            'prodi_id' => $request->prodi_id,
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function show(Rombel $rombel)
    {
        return response()->json([
            'code' => 200,
            'data' => $rombel,
            'message' => 'success'
        ], 200);
    }

    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'nama' => 'required',
            'prodi_id' => 'required'
        ]);

        $rombel->update($request->all());

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rombel  $rombel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rombel $rombel)
    {
        DB::beginTransaction();
        try {
            $rombel->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function dataDosenPa($id)
    {
        $datas = DB::table('rombel_tahun_ajarans')
            ->select('rombel_tahun_ajarans.id', 'users.name as dosen_pa', 'users.login_key as nip_pa', 'tahun_ajarans.nama as tahun_masuk')
            ->join('rombels', 'rombels.id', 'rombel_tahun_ajarans.rombel_id')
            ->join('users', 'users.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->join('tahun_ajarans', 'tahun_ajarans.id', 'rombel_tahun_ajarans.tahun_masuk_id')
            ->where('rombel_tahun_ajarans.rombel_id', $id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_rombel')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.rombel.dosen-pa.show', ['rombel_id' => $id, 'id' => $data->id]) . "`, `Set Dosen PA`, `#dosenPa`, getTahunAjaran)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_rombel')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('dosen_pa', function ($datas) {
                return $datas->dosen_pa . ' (' . $datas->nip_pa . ')';
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function showDosenPa($rombel_id, $id)
    {
        $data = DB::table('rombel_tahun_ajarans')->where('id', $id)->first();

        if ($data->rombel_id != $rombel_id) {
            return response()->json([
                'message' => 'Maaf telah terjadi kesalahan!'
            ], 400);
        }

        return response()->json([
            'message' => 'Berhasil',
            'data' => $data
        ], 200);
    }

    public function indexDosenPa($id)
    {
        $dosen = User::select('users.*')
            ->join('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->where('profile_dosens.status', '1')
            ->role('dosen')->get();
        return view('data_master.rombel.set-dosen-pa', compact('dosen'));
    }

    public function storeDosenPa(Request $request, $rombel_id)
    {
        $request->validate([
            'tahun_masuk_id' => 'required',
            'dosen_pa_id' => 'required'
        ]);

        DB::table('rombel_tahun_ajarans')->insert([
            'rombel_id' => $rombel_id,
            'dosen_pa_id' => $request->dosen_pa_id,
            'tahun_masuk_id' => $request->tahun_masuk_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'berhasil disimpan'
        ], 200);
    }

    public function updateDosenPa(Request $request, $rombel_id, $dosen_pa_id)
    {
        $request->validate([
            'dosen_pa_id' => 'required'
        ]);

        DB::table('rombel_tahun_ajarans')
            ->where('id', $dosen_pa_id)
            ->where('rombel_id', $rombel_id)
            ->update([
                'dosen_pa_id' => $request->dosen_pa_id
            ]);

        return response()->json([
            'message' => 'berhasil diubah'
        ], 200);
    }

    public function getTahunAjaran()
    {
        $tahun_ajarans = TahunAjaran::select('tahun_ajarans.*')
            ->leftJoin('rombel_tahun_ajarans', 'rombel_tahun_ajarans.tahun_masuk_id', '=', 'tahun_ajarans.id')
            ->whereNull('rombel_tahun_ajarans.tahun_masuk_id')
            ->when(request('dosen_pa_id') && request('dosen_pa_id') !== '', function ($query) {
                $oldData = DB::table('rombel_tahun_ajarans')->where('id', request('dosen_pa_id'))->first();
                $query->orWhere('tahun_ajarans.id', $oldData->tahun_masuk_id);
            })
            ->get();

        return response()->json([
            'message' => 'success',
            'data' => $tahun_ajarans
        ], 200);
    }

    public function getDosenPa()
    {
        $data = DB::table('rombels')
            ->select('rombels.*', 'users.name as dosen_pa', 'users.login_key as nip_pa')
            ->join('rombel_tahun_ajarans', 'rombel_tahun_ajarans.rombel_id', '=', 'rombels.id')
            ->join('users', 'users.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->when(request('tahun_ajaran_id'), function ($q) {
                $q->where('rombel_tahun_ajarans.tahun_masuk_id', request('tahun_ajaran_id'));
            })
            ->when(request('prodi_id'), function ($q) {
                $q->where('rombels.prodi_id', request('prodi_id'));
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }
}
