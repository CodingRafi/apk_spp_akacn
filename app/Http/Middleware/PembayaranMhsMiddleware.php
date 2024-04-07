<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranMhsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            $mhs_id = $user->id;
        }

        if (!$user->hasRole('mahasiswa') && request('mhs_id')) {
            abort(404);
        }

        if (!isset($mhs_id)) {
            abort(404);
        }

        $mhs = DB::table('profile_mahasiswas')->where('user_id', $mhs_id)->first();
        
        if (!$mhs) {
            abort(403);
        }

        if ($request->type == 'semester') {
            $cek = DB::table('tahun_pembayaran')
                ->join('tahun_semester', 'tahun_semester.id', '=', 'tahun_pembayaran.tahun_semester_id')
                ->where('tahun_semester.prodi_id', $mhs->prodi_id)
                ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
                ->where('tahun_pembayaran.id', $request->id)
                ->first();
            if (!$cek) {
                abort(404);
            }
        } elseif ($request->type == 'lainnya') {
            $cek = DB::table('tahun_pembayaran_lain')
                ->where('prodi_id', $mhs->prodi_id)
                ->where('id', $request->id)
                ->first();

            if (!$cek) {
                abort(404);
            }
        } else {
            abort(404);
        }
        return $next($request);
    }
}
