<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Kelola\{
    KrsController as KelolaKrsController,
    KuesionerController,
    KurikulumController,
    MatkulController,
    PembayaranController as KelolaPembayaranController,
    PembayaranLainnyaController as KelolaPembayaranLainnyaController,
    RoleController,
    TahunAjaranController,
    ProdiController,
    PotonganController as KelolaPotonganController,
    PresensiController as KelolaPresensiController,
    RekapPresensiController,
    RombelController,
    RuangController,
    SemesterController as KelolaSemesterController,
    TemplateSuratController
};

use App\Http\Controllers\{
    HomeController,
    DashboardController,
    ProfileController,
    TemplateSuratController as ControllersTemplateSuratController,
    WhitelistIPController
};
use App\Http\Controllers\Dosen\PresensiController;
use App\Http\Controllers\Kelola\Angkatan\MatkulController as AngkatanMatkulController;
use App\Http\Controllers\Kelola\Angkatan\PembayaranLainnyaController;
use App\Http\Controllers\Kelola\Angkatan\PembayaranSemesterController;
use App\Http\Controllers\Kelola\Angkatan\PotonganController;
use App\Http\Controllers\Kelola\Angkatan\SemesterController;
use App\Http\Controllers\Kelola\Mahasiswa\PotonganController as MahasiswaPotonganController;
use App\Http\Controllers\Kelola\User\{
    AsdosController,
    DosenController,
    MahasiwaController,
    PetugasController,
};

use App\Http\Controllers\Kelola\UserController;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\PembayaranController as MahasiswaPembayaranController;
use App\Http\Controllers\Mahasiswa\PresensiController as MahasiswaPresensiController;

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
        Route::get('{role}/exportPembayaran', [UserController::class, 'exportPembayaran'])->name('exportPembayaran');
        Route::get('{role}/create', [UserController::class, 'create'])->name('create');
        Route::get('{role}/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::resource('mahasiswa', MahasiwaController::class)->except('index', 'create', 'edit');
        Route::resource('dosen', DosenController::class)->except('index', 'create', 'edit');
        Route::resource('asdos', AsdosController::class)->except('index', 'create', 'edit');
        Route::resource('petugas', PetugasController::class)->except('index', 'create', 'edit');

        Route::prefix('{role}/potongan')->name('potongan.')->group(function () {
            Route::get('/{user_id}/get', [MahasiswaPotonganController::class, 'get'])->name('get');
            Route::get('/{user_id}/data', [MahasiswaPotonganController::class, 'data'])->name('data');
            Route::get('/{user_id}', [MahasiswaPotonganController::class, 'index'])->name('index');
            Route::post('/{user_id}', [MahasiswaPotonganController::class, 'store'])->name('store');
            Route::delete('/{user_id}/{potongan_id}', [MahasiswaPotonganController::class, 'destroy'])->name('destroy');
        });
    });

    Route::post('/upload-file', [HomeController::class, 'upload_file'])->name('upload_file');

    Route::prefix('data-master')->name('data-master.')->group(function () {
        //? Tahun ajaran
        Route::get('tahun-ajaran/data', [TahunAjaranController::class, 'data'])->name('tahun-ajaran.data');
        Route::resource('tahun-ajaran', TahunAjaranController::class);

        //? Tahun ajaran matkul
        Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
            Route::prefix('{id}/matkul')->name('matkul.')->group(function () {
                Route::get('/', [AngkatanMatkulController::class, 'index'])->name('index');
                Route::get('/{kurikulum_id}/get-matkul', [AngkatanMatkulController::class, 'getMatkul'])->name('getMatkul');
                Route::get('/{matkul_id}/get-rombel', [AngkatanMatkulController::class, 'getRombel'])->name('getRombel');
                Route::get('/data', [AngkatanMatkulController::class, 'data'])->name('data');
                Route::post('/', [AngkatanMatkulController::class, 'store'])->name('store');
                Route::get('/{matkul_id}', [AngkatanMatkulController::class, 'show'])->name('show');
                Route::put('/{matkul_id}', [AngkatanMatkulController::class, 'update'])->name('update');
            });
        });

        //? Semester
        Route::prefix('semester')->name('semester.')->group(function () {
            Route::get('{tahun_ajaran_id}/data', [KelolaSemesterController::class, 'data'])->name('data');
            Route::get('{tahun_ajaran_id}/getLastSemester', [KelolaSemesterController::class, 'getLastSemester'])->name('getLastSemester');
            Route::post('/', [KelolaSemesterController::class, 'store'])->name('store');
            Route::get('/{semester_id}', [KelolaSemesterController::class, 'show'])->name('show');
            Route::put('/{semester_id}', [KelolaSemesterController::class, 'update'])->name('update');
        });

        //? Kurikulum
        Route::get('kurikulum/data', [KurikulumController::class, 'data'])->name('kurikulum.data');
        Route::resource('kurikulum', KurikulumController::class);

        //? Ruang
        Route::get('ruang/data', [RuangController::class, 'data'])->name('ruang.data');
        Route::resource('ruang', RuangController::class);

        //? Template Surat
        Route::get('template-surat/data', [TemplateSuratController::class, 'data'])->name('template-surat.data');
        Route::resource('template-surat', TemplateSuratController::class);

        //? Kuesioner
        Route::get('kuesioner/{id}/change-status', [KuesionerController::class, 'change_status'])->name('kuesioner.change-status');
        Route::get('kuesioner/data', [KuesionerController::class, 'data'])->name('kuesioner.data');
        Route::resource('kuesioner', KuesionerController::class);

        //? Mata Kuliah
        Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
            Route::get('{kurikulum_id}/data', [MatkulController::class, 'data'])->name('data');
            Route::post('/', [MatkulController::class, 'store'])->name('store');
            Route::get('/{matkul_id}', [MatkulController::class, 'show'])->name('show');
            Route::put('/{matkul_id}', [MatkulController::class, 'update'])->name('update');
        });

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
                Route::get('/{tahun_semester_id}', [SemesterController::class, 'show'])->name('show');
                Route::put('/{tahun_semester_id}', [SemesterController::class, 'update'])->name('update');
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

            //? Prodi - Pembayaran lainnya
            Route::prefix('pembayaran-lainnya')->name('pembayaran-lainnya.')->group(function () {
                Route::get('/jenis', [PembayaranLainnyaController::class, 'getJenis'])->name('getJenis');
                Route::get('/data', [PembayaranLainnyaController::class, 'data'])->name('data');
                Route::post('/', [PembayaranLainnyaController::class, 'store'])->name('store');
                Route::get('/{id}', [PembayaranLainnyaController::class, 'show'])->name('show');
                Route::put('/{id}', [PembayaranLainnyaController::class, 'update'])->name('update');
            });

            //? Prodi - Potongan
            Route::prefix('potongan')->name('potongan.')->group(function () {
                Route::get('/data', [PotonganController::class, 'data'])->name('data');
                Route::post('/', [PotonganController::class, 'store'])->name('store');
                Route::get('/{id}', [PotonganController::class, 'show'])->name('show');
                Route::put('/{id}', [PotonganController::class, 'update'])->name('update');
                Route::delete('/{id}', [PotonganController::class, 'destroy'])->name('destroy');
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

        //? Presensi
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [KelolaPresensiController::class, 'index'])->name('index');
            Route::get('/get-tahun-ajaran', [KelolaPresensiController::class, 'getTahunAjaran'])->name('getTahunAjaran');
            Route::get('/get-pengawas', [KelolaPresensiController::class, 'getPengawas'])->name('getPengawas');
            Route::get('/{tahun_ajaran_id}', [KelolaPresensiController::class, 'show'])->name('show');
            Route::get('/{tahun_ajaran_id}/{tahun_matkul_id}/total', [KelolaPresensiController::class, 'getTotalPelajaran'])->name('getTotalPelajaran');
            Route::get('/{tahun_ajaran_id}/{tahun_matkul_id}/get-pengajar', [KelolaPresensiController::class, 'getPengajar'])->name('getPengajar');
            Route::get('/{tahun_ajaran_id}/{tahun_matkul_id}/get-ujian', [KelolaPresensiController::class, 'getJenisUjian'])->name('getJenisUjian');
            Route::post('/{tahun_ajaran_id}', [KelolaPresensiController::class, 'store'])->name('store');
            Route::get('/{tahun_ajaran_id}/get-jadwal', [KelolaPresensiController::class, 'getJadwal'])->name('getJadwal');
            Route::get('/{tahun_ajaran_id}/{jadwal_id}', [KelolaPresensiController::class, 'showJadwal'])->name('showJadwal');
            Route::put('/{jadwal_id}', [KelolaPresensiController::class, 'updateJadwal'])->name('updateJadwal');
            Route::put('/{jadwal_id}/mulai', [KelolaPresensiController::class, 'mulaiJadwal'])->name('mulaiJadwal');
            Route::put('/{jadwal_id}/selesai', [KelolaPresensiController::class, 'selesaiJadwal'])->name('selesaiJadwal');
            Route::get('/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/get-presensi', [KelolaPresensiController::class, 'getPresensi'])->name('getPresensi');
            Route::get('/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/{mhs_id}', [KelolaPresensiController::class, 'getPresensiMhs'])->name('getPresensiMhs');
            Route::put('/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/{mhs_id}', [KelolaPresensiController::class, 'updatePresensiMhs'])->name('updatePresensiMhs');
        });

        //? Rekap Presensi
        Route::prefix('rekap')->name('rekap.')->group(function () {
            Route::get('/get-rombel', [RekapPresensiController::class, 'getRombel'])->name('getRombel');
            Route::prefix('{tahun_ajaran_id}')->group(function () {
                Route::get('/', [RekapPresensiController::class, 'index'])->name('index');
                Route::get('/get-presensi', [RekapPresensiController::class, 'getPresensi'])->name('getPresensi');
                Route::get('/{tahun_matkul_id}', [RekapPresensiController::class, 'show'])->name('show');
            });
        });
    });

    Route::prefix('kelola-pembayaran')->name('kelola-pembayaran.')->group(function () {
        Route::get('potongan/data', [KelolaPotonganController::class, 'data'])->name('potongan.data');
        Route::get('/potongan/get', [KelolaPotonganController::class, 'get'])->name('potongan.get');
        Route::resource('potongan', KelolaPotonganController::class);

        Route::prefix('verifikasi-pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [KelolaPembayaranController::class, 'index'])->name('index');
            Route::get('/data', [KelolaPembayaranController::class, 'data'])->name('data');
            Route::get('/export', [KelolaPembayaranController::class, 'export'])->name('export');
            Route::get('/{pembayaran_id}', [KelolaPembayaranController::class, 'show'])->name('show');
            Route::post('/{pembayaran_id}', [KelolaPembayaranController::class, 'store'])->name('store');
            Route::get('/{pembayaran_id}/revisi', [KelolaPembayaranController::class, 'revisi'])->name('revisi');
        });

        Route::get('pembayaran-lainnya/data', [KelolaPembayaranLainnyaController::class, 'data'])->name('pembayaran-lainnya.data');
        Route::resource('pembayaran-lainnya', KelolaPembayaranLainnyaController::class);
    });

    Route::prefix('verifikasi-krs')->name('verifikasi-krs.')->group(function () {
        Route::get('/', [KelolaKrsController::class, 'index'])->name('index');
        Route::get('/data', [KelolaKrsController::class, 'data'])->name('data');
        Route::get('/{id}', [KelolaKrsController::class, 'show'])->name('show');
        Route::get('/{id}/dataMatkul', [KelolaKrsController::class, 'dataMatkul'])->name('dataMatkul');
        Route::delete('/{id}/{krs_matkul_id}', [KelolaKrsController::class, 'destroy'])->name('destroy');;
    });

    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('data', [MahasiswaPembayaranController::class, 'data'])->name('data');
            Route::get('export', [MahasiswaPembayaranController::class, 'export'])->name('export');
            Route::get('/', [MahasiswaPembayaranController::class, 'index'])->name('index');
            Route::prefix('{type}/{id}')->middleware(['pembayaran.mhs'])->group(function () {
                Route::get('/dataPembayaran', [MahasiswaPembayaranController::class, 'dataPembayaran'])->name('dataPembayaran');
                Route::get('/create', [MahasiswaPembayaranController::class, 'create'])->name('create');
                Route::post('/', [MahasiswaPembayaranController::class, 'store'])->name('store');
                Route::get('/', [MahasiswaPembayaranController::class, 'show'])->name('show');
                Route::get('/{pembayaran_id}', [MahasiswaPembayaranController::class, 'showPembayaran'])->name('showPembayaran');
                Route::get('/{pembayaran_id}/revisi', [MahasiswaPembayaranController::class, 'revisi'])->name('revisi');
                Route::patch('/{pembayaran_id}/revisi', [MahasiswaPembayaranController::class, 'storeRevisi'])->name('storeRevisi');
                Route::get('/{pembayaran_id}/edit', [MahasiswaPembayaranController::class, 'edit'])->name('edit');
                Route::patch('/{pembayaran_id}', [MahasiswaPembayaranController::class, 'update'])->name('update');
                Route::delete('/{pembayaran_id}', [MahasiswaPembayaranController::class, 'destroy'])->name('destroy');
                Route::get('/{pembayaran_id}/print', [MahasiswaPembayaranController::class, 'print'])->name('print');
            });
        });

        Route::prefix('krs')->name('krs.')->group(function () {
            Route::get('/', [KrsController::class, 'index'])->name('index');
            Route::get('dataSemester', [KrsController::class, 'dataSemester'])->name('dataSemester');
            Route::get('/{tahun_semester_id}', [KrsController::class, 'show'])->name('show');
            Route::get('/{tahun_semester_id}/dataMatkul', [KrsController::class, 'dataMatkul'])->name('dataMatkul');
            Route::post('/{tahun_semester_id}/ajukan', [KrsController::class, 'ajukan'])->name('ajukan');
            Route::post('/{tahun_semester_id}', [KrsController::class, 'store'])->name('store');
            Route::delete('/{tahun_semester_id}/{krs_matkul_id}', [KrsController::class, 'destroy'])->name('destroy');
            Route::get('/{tahun_semester_id}/getMatkul', [KrsController::class, 'getMatkul'])->name('getMatkul');
            Route::get('/{tahun_semester_id}/getTotalSKS', [KrsController::class, 'getTotalSKS'])->name('getTotalSKS');
        });

        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [MahasiswaPresensiController::class, 'index'])->name('index');
            Route::get('/data', [MahasiswaPresensiController::class, 'data'])->name('data');
            Route::post('/', [MahasiswaPresensiController::class, 'store'])->name('store');
        });
    });

    Route::get('template-surat', [ControllersTemplateSuratController::class, 'index'])->name('template-surat.index');
});

require __DIR__ . '/auth.php';
