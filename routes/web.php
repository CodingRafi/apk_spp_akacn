<?php

use App\Models\BukuTamu;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Kelola\{
    PembayaranController as KelolaPembayaranController,
    RoleController,
    UserController,
    TahunAjaranController,
    ProdiController,
    SemesterController,
    BiayaController
};

use App\Http\Controllers\{
    PembayaranController,
    HomeController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('index');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard', function(){
        return view('dashboard');
    });
    Route::resource('roles', RoleController::class);
    Route::prefix('users')->name('users.')->middleware('user.role')->group(function () {
        Route::get('{role}', [UserController::class, 'index'])->name('index');
        Route::get('{role}/create', [UserController::class, 'create'])->name('create');
        Route::post('{role}', [UserController::class, 'store'])->name('store');
        Route::get('{role}/data', [UserController::class, 'data'])->name('data');
        Route::get('{role}/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('{role}/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('{role}/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    Route::post('/upload-file', [HomeController::class, 'upload_file'])->name('upload_file');

    Route::prefix('data-master')->name('data-master.')->group(function () {
        //? Tahun ajaran
        Route::get('tahun-ajaran/data', [TahunAjaranController::class, 'data'])->name('tahun-ajaran.data');
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        
        //? Prodi
        Route::get('prodi/data', [ProdiController::class, 'data'])->name('prodi.data');
        Route::resource('prodi', ProdiController::class);

        Route::prefix('prodi')->name('prodi.')->group(function () {
            //? Semester
            Route::name('semester.')->group(function () {
                Route::get('{prodi_id}/semester/data', [SemesterController::class, 'data'])->name('data');
                Route::get('{prodi_id}/semester/create', [SemesterController::class, 'create'])->name('create');
                Route::post('{prodi_id}/semester', [SemesterController::class, 'store'])->name('store');
                Route::get('{prodi_id}/semester/{semester_id}', [SemesterController::class, 'show'])->name('show');
                Route::get('{prodi_id}/semester/{semester_id}/edit', [SemesterController::class, 'edit'])->name('edit');
                Route::patch('{prodi_id}/semester/{semester_id}', [SemesterController::class, 'update'])->name('update');
                Route::delete('{prodi_id}/semester/{semester_id}', [SemesterController::class, 'destroy'])->name('destroy');

                //? Biaya
                Route::name('biaya.')->group(function () {
                    Route::get('{prodi_id}/semester/{semester_id}/biaya/data', [BiayaController::class, 'data'])->name('data');
                    Route::get('{prodi_id}/semester/{semester_id}/biaya/create', [BiayaController::class, 'create'])->name('create');
                    Route::post('{prodi_id}/semester/{semester_id}/biaya', [BiayaController::class, 'store'])->name('store');
                    Route::get('{prodi_id}/semester/{semester_id}/biaya/{tahun_ajaran_id}', [BiayaController::class, 'show'])->name('show');
                    Route::get('{prodi_id}/semester/{semester_id}/biaya/{tahun_ajaran_id}/edit', [BiayaController::class, 'edit'])->name('edit');
                    Route::patch('{prodi_id}/semester/{semester_id}/biaya/{tahun_ajaran_id}', [BiayaController::class, 'update'])->name('update');
                    Route::delete('{prodi_id}/semester/{semester_id}/biaya/{tahun_ajaran_id}', [BiayaController::class, 'destroy'])->name('destroy');
                });
            });
        });

    });
    
    Route::prefix('kelola')->name('kelola.')->group(function () {
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [KelolaPembayaranController::class, 'index'])->name('index');
            Route::get('/data', [KelolaPembayaranController::class, 'data'])->name('data');
            Route::get('/export', [KelolaPembayaranController::class, 'export'])->name('export');
            Route::get('/{pembayaran_id}', [KelolaPembayaranController::class, 'show'])->name('show');
            Route::post('/{pembayaran_id}', [KelolaPembayaranController::class, 'store'])->name('store');
            Route::get('/{pembayaran_id}/revisi', [KelolaPembayaranController::class, 'revisi'])->name('revisi');
        });
    });
    
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('data', [PembayaranController::class, 'data'])->name('data');
            Route::get('/', [PembayaranController::class, 'index'])->name('index');
            Route::middleware(['pembayaran.semester'])->group(function () {
                Route::get('{semester_id}/data', [PembayaranController::class, 'dataPembayaran'])->name('dataPembayaran');
                Route::get('{semester_id}', [PembayaranController::class, 'show'])->name('show');
                Route::get('{semester_id}/create', [PembayaranController::class, 'create'])->name('create');
                Route::post('{semester_id}', [PembayaranController::class, 'store'])->name('store');
                Route::get('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'showPembayaran'])->name('showPembayaran');
                Route::get('{semester_id}/{pembayaran_id}/edit', [PembayaranController::class, 'edit'])->name('edit');
                Route::patch('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'update'])->name('update');
                Route::delete('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'destroy'])->name('destroy');
                Route::get('{semester_id}/{pembayaran_id}/revisi', [PembayaranController::class, 'revisi'])->name('revisi');
                Route::patch('{semester_id}/{pembayaran_id}/revisi', [PembayaranController::class, 'storeRevisi'])->name('storeRevisi');
            });
        });
    });
});

require __DIR__.'/auth.php';
