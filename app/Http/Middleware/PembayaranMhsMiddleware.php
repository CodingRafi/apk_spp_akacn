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
        $mhs = Auth::user()->mahasiswa;
        if ($request->type == 'semester') {
            $cek = DB::table('tahun_semester')
                ->where('prodi_id', $mhs->prodi_id)
                ->where('tahun_ajaran_id', $mhs->tahun_masuk_id)
                ->where('id', $request->id)
                ->first();

            if (!$cek) {
                abort(404);
            }
        } else if ($request->type == 'lainnya') {
        } else {
            abort(404);
        }
        return $next($request);
    }
}
