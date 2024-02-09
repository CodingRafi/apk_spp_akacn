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

use App\Http\Controllers\Kelola\User\{
    MahasiwaController
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

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);

    Route::prefix('kelola-users')->name('kelola-users.')->group(function () {
        // Mahasiswa
        Route::resource('mahasiswa', MahasiwaController::class);
        Route::resource('dosen', MahasiwaController::class);
        Route::resource('asdos', MahasiwaController::class);
        Route::resource('petugas', MahasiwaController::class);

        // Route::get('{role}', [UserController::class, 'index'])->name('index');
        // Route::get('{role}/create', [UserController::class, 'create'])->name('create');
        // Route::get('{role}/data', [UserController::class, 'data'])->name('data');
        // Route::post('{role}', [UserController::class, 'store'])->name('store');
        // Route::get('{role}/import', [UserController::class, 'import'])->name('import');
        // Route::post('{role}/import', [UserController::class, 'saveImport'])->name('saveImport');
        // Route::get('{role}/export/pembayaran', [UserController::class, 'exportPembayaran'])->name('export.pembayaran');
        // Route::get('{role}/{user_id}/print', [UserController::class, 'printPembayaran'])->name('print.pembayaran');
        // Route::get('{role}/{id}/edit', [UserController::class, 'edit'])->name('edit');
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
        Route::resource('prodi', ProdiController::class);

        //? Rombel
        Route::get('rombel/data', [RombelController::class, 'data'])->name('rombel.data');
        Route::get('rombel/{id}/set-dosen-pa', [RombelController::class, 'setDosenPa'])->name('rombel.setDosenPa');
        Route::post('rombel/set-dosen-pa', [RombelController::class, 'storeDosenPa'])->name('rombel.storeDosenPa');
        Route::get('rombel/get-tahun-ajaran', [RombelController::class, 'getTahunAjaran'])->name('rombel.getTahunAjaran');
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
