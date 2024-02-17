<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Kelola\{
    PembayaranController as KelolaPembayaranController,
    RoleController,
    TahunAjaranController,
    ProdiController,
    PotonganController,
    RombelController
};

use App\Http\Controllers\{
    PembayaranController,
    HomeController,
    DashboardController,
    ProfileController,
    WhitelistIPController
};
use App\Http\Controllers\Kelola\Angkatan\PembayaranSemesterController;
use App\Http\Controllers\Kelola\Angkatan\SemesterController;
use App\Http\Controllers\Kelola\User\{
    AsdosController,
    DosenController,
    MahasiwaController,
    PetugasController,
};

use App\Http\Controllers\Kelola\UserController;

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

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);

    Route::prefix('kelola-users')->name('kelola-users.')->group(function () {
        Route::get('{role}', [UserController::class, 'index'])->name('index');
        Route::get('{role}/data', [UserController::class, 'data'])->name('data');
        Route::get('{role}/create', [UserController::class, 'create'])->name('create');
        Route::get('{role}/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::resource('mahasiswa', MahasiwaController::class)->except('index', 'create', 'edit');
        Route::resource('dosen', DosenController::class)->except('index', 'create', 'edit');
        Route::resource('asdos', AsdosController::class)->except('index', 'create', 'edit');
        Route::resource('petugas', PetugasController::class)->except('index', 'create', 'edit');

        // Route::post('{role}', [UserController::class, 'store'])->name('store');
        // Route::get('{role}/import', [UserController::class, 'import'])->name('import');
        // Route::post('{role}/import', [UserController::class, 'saveImport'])->name('saveImport');
        // Route::get('{role}/export/pembayaran', [UserController::class, 'exportPembayaran'])->name('export.pembayaran');
        // Route::get('{role}/{user_id}/print', [UserController::class, 'printPembayaran'])->name('print.pembayaran');
        // Route::patch('{role}/{id}', [UserController::class, 'update'])->name('update');
        // Route::delete('{role}/{id}', [UserController::class, 'destroy'])->name('destroy');

        // Route::name('potongan.')->group(function () {
        //     Route::get('{role}/{id}/potongan', [UserPotonganController::class, 'index'])->name('index');
        //     Route::get('{role}/{id}/potongan/{semester_id}', [UserPotonganController::class, 'data'])->name('data');
        //     Route::post('{role}/{id}/potongan/set', [UserPotonganController::class, 'store'])->name('store');
        // });
    });

    Route::post('/upload-file', [HomeController::class, 'upload_file'])->name('upload_file');

    Route::prefix('data-master')->name('data-master.')->group(function () {
        //? Tahun ajaran
        Route::get('tahun-ajaran/data', [TahunAjaranController::class, 'data'])->name('tahun-ajaran.data');
        Route::resource('tahun-ajaran', TahunAjaranController::class);

        //? Prodi
        Route::get('prodi/data', [ProdiController::class, 'data'])->name('prodi.data');
        Route::get('prodi/{prodi_id}/angkatan', [ProdiController::class, 'angkatan'])->name('prodi.angkatan');
        Route::get('prodi/{prodi_id}/angkatan/{tahun_ajaran_id}', [ProdiController::class, 'angkatanDetail'])->name('prodi.angkatan.detail');
        Route::resource('prodi', ProdiController::class);

        Route::prefix('prodi/{prodi_id}/angkatan/{tahun_ajaran_id}')->name('prodi.')->group(function () {
            //? Prodi - Semester
            Route::prefix('semester')->name('semester.')->group(function () {
                Route::get('/', [SemesterController::class, 'index'])->name('index');
                Route::get('/data', [SemesterController::class, 'data'])->name('data');
                Route::post('/', [SemesterController::class, 'store'])->name('store');
                Route::delete('/{tahun_semester_id}', [SemesterController::class, 'destroy'])->name('destroy');
            });
            
            //? Prodi - Pembayaran Semester
            Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
                Route::get('/semester', [PembayaranSemesterController::class, 'getSemester'])->name('getSemester');
                Route::get('/data', [PembayaranSemesterController::class, 'data'])->name('data');
                Route::post('/', [PembayaranSemesterController::class, 'store'])->name('store');
                Route::get('/{id}', [PembayaranSemesterController::class, 'show'])->name('show');
                Route::put('/{id}', [PembayaranSemesterController::class, 'update'])->name('update');
            });
        });



        //? Rombel
        Route::get('rombel/data', [RombelController::class, 'data'])->name('rombel.data');
        Route::get('rombel/get-tahun-ajaran', [RombelController::class, 'getTahunAjaran'])->name('rombel.getTahunAjaran');
        Route::get('rombel/get-dosen-pa', [RombelController::class, 'getDosenPa'])->name('rombel.getDosenPa');
        Route::prefix('rombel/')->name('rombel.dosen-pa.')->group(function () {
            Route::get('{rombel_id}/dosen-pa', [RombelController::class, 'indexDosenPa'])->name('index');
            Route::post('{rombel_id}/dosen-pa', [RombelController::class, 'storeDosenPa'])->name('store');
            Route::get('{rombel_id}/dosen-pa/data', [RombelController::class, 'dataDosenPa'])->name('data');
            Route::get('{rombel_id}/dosen-pa/{id}', [RombelController::class, 'showDosenPa'])->name('show');
            Route::put('{rombel_id}/dosen-pa/{id}', [RombelController::class, 'updateDosenPa'])->name('update');
        });
        Route::resource('rombel', RombelController::class);
    });

    Route::prefix('kelola-presensi')->name('kelola-presensi.')->group(function () {
        //? Whitelist IP
        Route::get('whitelist-ip/data', [WhitelistIPController::class, 'data'])->name('whitelist-ip.data');
        Route::get('whitelist-ip/get', [WhitelistIPController::class, 'get_ip'])->name('whitelist-ip.get-ip');
        Route::resource('whitelist-ip', WhitelistIPController::class);
    });

    Route::prefix('kelola-pembayaran')->name('kelola-pembayaran.')->group(function () {
        Route::get('potongan/{prodi_id}/getSemester', [PotonganController::class, 'getSemester'])->name('potongan.getSemester');
        Route::get('potongan/data', [PotonganController::class, 'data'])->name('potongan.data');
        Route::resource('potongan', PotonganController::class);

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
            Route::get('export', [PembayaranController::class, 'export'])->name('export');
            Route::get('/', [PembayaranController::class, 'index'])->name('index');
            Route::middleware(['pembayaran.semester'])->group(function () {
                Route::get('{semester_id}/data', [PembayaranController::class, 'dataPembayaran'])->name('dataPembayaran');
                Route::get('{semester_id}', [PembayaranController::class, 'show'])->name('show');
                Route::get('{semester_id}/create', [PembayaranController::class, 'create'])->name('create');
                Route::post('{semester_id}', [PembayaranController::class, 'store'])->name('store');
                Route::get('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'showPembayaran'])->name('showPembayaran');
                Route::get('{semester_id}/{pembayaran_id}/print', [PembayaranController::class, 'print'])->name('print');
                Route::get('{semester_id}/{pembayaran_id}/edit', [PembayaranController::class, 'edit'])->name('edit');
                Route::patch('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'update'])->name('update');
                Route::delete('{semester_id}/{pembayaran_id}', [PembayaranController::class, 'destroy'])->name('destroy');
                Route::get('{semester_id}/{pembayaran_id}/revisi', [PembayaranController::class, 'revisi'])->name('revisi');
                Route::patch('{semester_id}/{pembayaran_id}/revisi', [PembayaranController::class, 'storeRevisi'])->name('storeRevisi');
            });
        });
    });
});

require __DIR__ . '/auth.php';
