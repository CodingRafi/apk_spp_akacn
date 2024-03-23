<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\Ref\ReferensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login']);
    
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('ref')->group(function () {
        Route::get('alat-transportasi', [ReferensiController::class, 'alatTransportasi']);
        Route::get('jenis-tinggal', [ReferensiController::class, 'jenisTinggal']);
        Route::get('jenis-kelas', [ReferensiController::class, 'jenisKelas']);
        Route::get('jenjang', [ReferensiController::class, 'jenjang']);
        Route::get('penghasilan', [ReferensiController::class, 'penghasilan']);
        Route::get('agama', [ReferensiController::class, 'agama']);
        Route::get('prodi', [ReferensiController::class, 'prodi']);
        Route::get('pekerjaan', [ReferensiController::class, 'pekerjaan']);
        Route::get('kewarganegaraan', [ReferensiController::class, 'kewarganegaraan']);
        Route::get('wilayah', [ReferensiController::class, 'wilayah']);
        Route::get('rombel', [ReferensiController::class, 'rombel']);
        Route::get('tahun-ajaran', [ReferensiController::class, 'tahunAjaran']);
    });

    Route::post('mahasiswa/store', [MahasiswaController::class, 'store']);
});
