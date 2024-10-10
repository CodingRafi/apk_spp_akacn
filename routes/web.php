<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Kelola\{
    BeritaAcaraController,
    GajiController,
    KrsController as KelolaKrsController,
    KuesionerController,
    KurikulumController,
    MateriController,
    MatkulController,
    MutuController,
    NilaiController,
    PembayaranController as KelolaPembayaranController,
    PembayaranLainnyaController as KelolaPembayaranLainnyaController,
    PenugasanDosenController,
    RoleController,
    TahunAjaranController,
    ProdiController,
    PotonganController as KelolaPotonganController,
    PresensiController as KelolaPresensiController,
    RekapPresensiController,
    ResponseKuesionerController,
    RombelController,
    RuangController,
    SemesterController as KelolaSemesterController,
    SettingController,
    TemplateSuratController
};

use App\Http\Controllers\{
    HomeController,
    DashboardController,
    JenisKelasController,
    KrsController as ControllersKrsController,
    ProfileController,
    TemplateSuratController as ControllersTemplateSuratController,
    WhitelistIPController,
    WilayahController
};
use App\Http\Controllers\Dosen\PresensiController;
use App\Http\Controllers\Kelola\Angkatan\MatkulController as AngkatanMatkulController;
use App\Http\Controllers\Kelola\Angkatan\MatkulDosenController;
use App\Http\Controllers\Kelola\Angkatan\MatkulMhsController;
use App\Http\Controllers\Kelola\Angkatan\MatkulNeoFeeder;
use App\Http\Controllers\Kelola\Angkatan\MatkulNeoFeederController;
use App\Http\Controllers\Kelola\Angkatan\MatkulRekapController;
use App\Http\Controllers\Kelola\Angkatan\MBKMController;
use App\Http\Controllers\Kelola\Angkatan\MBKMDosenPembimbingController;
use App\Http\Controllers\Kelola\Angkatan\MBKMDosenPengujiController;
use App\Http\Controllers\Kelola\Angkatan\MBKMMahasiswaController;
use App\Http\Controllers\Kelola\Angkatan\MBKMNeoFeederController;
use App\Http\Controllers\Kelola\Angkatan\PembayaranLainnyaController;
use App\Http\Controllers\Kelola\Angkatan\PembayaranSemesterController;
use App\Http\Controllers\Kelola\Angkatan\PotonganController;
use App\Http\Controllers\Kelola\Angkatan\SemesterController;
use App\Http\Controllers\Kelola\Mahasiswa\PembayaranTambahanController;
use App\Http\Controllers\Kelola\Mahasiswa\PotonganController as MahasiswaPotonganController;
use App\Http\Controllers\Kelola\NeoFeeder\DosenController as NeoFeederDosenController;
use App\Http\Controllers\Kelola\NeoFeeder\MahasiswaController as NeoFeederMahasiswaController;
use App\Http\Controllers\Kelola\User\{
    AdminController,
    AsdosController,
    DosenController,
    MahasiwaController,
    PetugasController,
};

use App\Http\Controllers\Kelola\UserController;
use App\Http\Controllers\Mahasiswa\BimbinganController;
use App\Http\Controllers\Mahasiswa\KhsController;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\KuesionerController as MahasiswaKuesionerController;
use App\Http\Controllers\Mahasiswa\MBKMController as MahasiswaMBKMController;
use App\Http\Controllers\Mahasiswa\PembayaranController as MahasiswaPembayaranController;
use App\Http\Controllers\Mahasiswa\PresensiController as MahasiswaPresensiController;
use App\Http\Controllers\Mahasiswa\TranskipController;
use App\Http\Controllers\NeoFeeder\NeoFeederController;
use App\Http\Controllers\Pengajar\GajiController as PengajarGajiController;

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

Route::group(['middleware' => ['auth', 'check.status']], function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
        Route::get('/admin/get-matkul', [DashboardController::class, 'adminGetMatkul'])->name('adminGetMatkul');
        Route::get('/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/dosen', [DashboardController::class, 'dosen'])->name('dosen');
        Route::get('/petugas', [DashboardController::class, 'petugas'])->name('petugas');
        Route::get('/asdos', [DashboardController::class, 'asdos'])->name('asdos');
    });

    Route::resource('roles', RoleController::class);

    Route::prefix('kelola-users')->name('kelola-users.')->group(function () {
        Route::get('{role}', [UserController::class, 'index'])->name('index');
        Route::get('{role}/data', [UserController::class, 'data'])->name('data');
        Route::get('{role}/exportPembayaran', [UserController::class, 'exportPembayaran'])->name('exportPembayaran');
        Route::get('{role}/create', [UserController::class, 'create'])->name('create');
        Route::get('{role}/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::get('{role}/{id}', [UserController::class, 'show'])->name('show');
        Route::resource('admin', AdminController::class)->only('update');
        Route::resource('mahasiswa', MahasiwaController::class)->except('index', 'create', 'edit');
        Route::resource('dosen', DosenController::class)->except('index', 'create', 'edit');
        Route::resource('asdos', AsdosController::class)->except('index', 'create', 'edit');
        Route::resource('petugas', PetugasController::class)->except('index', 'create', 'edit');

        Route::prefix('{role}/potongan')->name('potongan.')->group(function () {
            Route::get('/{user_id}/get', [MahasiswaPotonganController::class, 'get'])->name('get');
            Route::get('/{user_id}/data', [MahasiswaPotonganController::class, 'data'])->name('data');
            Route::post('/{user_id}', [MahasiswaPotonganController::class, 'store'])->name('store');
            Route::delete('/{user_id}/{potongan_id}', [MahasiswaPotonganController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{role}/pembayaran-tambahan')->name('pembayaran-tambahan.')->group(function () {
            Route::get('/{user_id}/data', [PembayaranTambahanController::class, 'data'])->name('data');
            Route::get('/{user_id}/{id}', [PembayaranTambahanController::class, 'show'])->name('show');
            Route::post('/{user_id}', [PembayaranTambahanController::class, 'store'])->name('store');
            Route::put('/{user_id}/{id}', [PembayaranTambahanController::class, 'update'])->name('update');
            Route::delete('/{user_id}/{id}', [PembayaranTambahanController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('neo-feeder')->name('neo-feeder.')->group(function () {
            Route::prefix('dosen')->name('dosen.')->group(function () {
                Route::post('store', [NeoFeederDosenController::class, 'store'])->name('store');
            });

            Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
                Route::get('{user_id}', [NeoFeederMahasiswaController::class, 'show'])->name('show');
                Route::post('store', [NeoFeederMahasiswaController::class, 'store'])->name('store');
                Route::patch('{user_id}', [NeoFeederMahasiswaController::class, 'update'])->name('update');
            });
        });
    });

    Route::prefix('penugasan-dosen')->name('penugasan-dosen.')->group(function () {
        Route::get('/', [PenugasanDosenController::class, 'index'])->name('index');
        Route::get('/dataTahunAjaran', [PenugasanDosenController::class, 'dataTahunAjaran'])->name('dataTahunAjaran');
        Route::get('/{tahun_ajaran_id}', [PenugasanDosenController::class, 'show'])->name('show');
        Route::get('/{tahun_ajaran_id}/data', [PenugasanDosenController::class, 'data'])->name('data');
        Route::post('/{tahun_ajaran_id}/neo-feeder', [PenugasanDosenController::class, 'storeNeoFeeder'])
            ->name('storeNeoFeeder');
    });

    Route::post('/upload-file', [HomeController::class, 'upload_file'])->name('upload_file');

    Route::prefix('data-master')->name('data-master.')->group(function () {
        //? Tahun ajaran
        Route::post(
            'tahun-ajaran/neo-feeder',
            [TahunAjaranController::class, 'storeNeoFeeder']
        )->name('tahun-ajaran.storeNeoFeeder');
        Route::get('tahun-ajaran/data', [TahunAjaranController::class, 'data'])->name('tahun-ajaran.data');
        Route::resource('tahun-ajaran', TahunAjaranController::class);

        //? Tahun ajaran matkul
        Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
            Route::prefix('{id}/matkul')->name('matkul.')->group(function () {
                Route::get('/', [AngkatanMatkulController::class, 'index'])->name('index');
                Route::get(
                    '/{prodi_id}/get-rombel',
                    [AngkatanMatkulController::class, 'getRombel']
                )->name('getRombel');
                Route::get(
                    '/{prodi_id}/get-matkul',
                    [AngkatanMatkulController::class, 'getMatkul']
                )->name('getMatkul');
                Route::get('/data', [AngkatanMatkulController::class, 'data'])->name('data');
                Route::post('/', [AngkatanMatkulController::class, 'store'])->name('store');
                Route::get('/{matkul_id}', [AngkatanMatkulController::class, 'show'])->name('show');
                Route::put('/{matkul_id}', [AngkatanMatkulController::class, 'update'])->name('update');
                Route::delete('/{matkul_id}', [AngkatanMatkulController::class, 'destroy'])->name('destroy');

                //? Dosen
                Route::prefix('{matkul_id}/dosen')->name('dosen.')->group(function () {
                    Route::get('/', [MatkulDosenController::class, 'index'])->name('index');
                    Route::post('/', [MatkulDosenController::class, 'store'])->name('store');
                    Route::get('/data', [MatkulDosenController::class, 'data'])->name('data');
                    Route::get('/{tahun_matkul_dosen_id}', [MatkulDosenController::class, 'show'])->name('show');
                    Route::put('/{tahun_matkul_dosen_id}', [MatkulDosenController::class, 'update'])->name('update');
                    Route::delete('/{tahun_matkul_dosen_id}', [MatkulDosenController::class, 'destroy'])->name('destroy');
                });

                //? Mahasiswa Ulang
                Route::prefix('{matkul_id}/mhs')->name('mhs.')->group(function () {
                    Route::get('/', [MatkulMhsController::class, 'index'])->name('index');
                    Route::post('/', [MatkulMhsController::class, 'store'])->name('store');
                    Route::get('/data', [MatkulMhsController::class, 'data'])->name('data');
                    Route::get('/{tahun_masuk_id}/get-mhs', [MatkulMhsController::class, 'getMhs'])->name('getMhs');
                    Route::delete('/{tahun_matkul_mhs_id}', [MatkulMhsController::class, 'destroy'])->name('destroy');
                });
            });
        });

        //? Semester
        Route::prefix('semester')->name('semester.')->group(function () {
            Route::get('{tahun_ajaran_id}/data', [KelolaSemesterController::class, 'data'])->name('data');
            Route::get(
                '{tahun_ajaran_id}/getLastSemester',
                [KelolaSemesterController::class, 'getLastSemester']
            )->name('getLastSemester');
            Route::get(
                '{tahun_ajaran_id}/get-neo-feeder',
                [KelolaSemesterController::class, 'getNeoFeeder']
            )->name('get-neo-feeder');
            Route::get('{tahun_ajaran_id}/get', [KelolaSemesterController::class, 'get'])->name('get');
            Route::post('/', [KelolaSemesterController::class, 'store'])->name('store');
            Route::get('/{semester_id}', [KelolaSemesterController::class, 'show'])->name('show');
            Route::put('/{semester_id}', [KelolaSemesterController::class, 'update'])->name('update');
            Route::delete('/{semester_id}', [KelolaSemesterController::class, 'destroy'])->name('destroy');
            Route::post('/neo-feeder', [KelolaSemesterController::class, 'storeNeoFeeder'])->name('storeNeoFeeder');
        });

        //? Kurikulum
        Route::prefix('kurikulum')->name('kurikulum.')->group(function () {
            Route::get('data', [KurikulumController::class, 'data'])->name('data');
            Route::post('store-matkul', [KurikulumController::class, 'storeMatkul'])->name('storeMatkul');
            Route::post('neo-feeder', [KurikulumController::class, 'storeNeoFeeder'])->name('storeNeoFeeder');
            Route::post(
                'neo-feeder/matkul',
                [KurikulumController::class, 'storeMatkulNeoFeeder']
            )->name('storeMatkulNeoFeeder');
            Route::get('{kurikulum_id}/mata-kuliah', [KurikulumController::class, 'getMatkul'])->name('getMatkul');
            Route::get('{kurikulum_id}/data-matkul', [KurikulumController::class, 'dataMatkul'])->name('dataMatkul');
            Route::delete(
                '{kurikulum_id}/{matkul_id}',
                [KurikulumController::class, 'destroyMatkul']
            )->name('destroyMatkul');
        });
        Route::resource('kurikulum', KurikulumController::class);

        //? Ruang
        Route::get('ruang/data', [RuangController::class, 'data'])->name('ruang.data');
        Route::resource('ruang', RuangController::class);

        //? Jenis Kelas
        Route::get('jenis-kelas/data', [JenisKelasController::class, 'data'])->name('jenis-kelas.data');
        Route::resource('jenis-kelas', JenisKelasController::class);

        //? Template Surat
        Route::get('template-surat/data', [TemplateSuratController::class, 'data'])->name('template-surat.data');
        Route::resource('template-surat', TemplateSuratController::class);

        //? Mutu
        Route::get('mutu/data', [MutuController::class, 'data'])->name('mutu.data');
        Route::post('mutu/neo-feeder', [MutuController::class, 'storeNeoFeeder'])->name('mutu.storeNeoFeeder');
        Route::resource('mutu', MutuController::class);

        //? Mata Kuliah
        Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
            Route::get('/', [MatkulController::class, 'index'])->name('index');
            Route::post('/neo-feeder', [MatkulController::class, 'storeNeoFeeder'])->name('storeNeoFeeder');
            Route::get('/data', [MatkulController::class, 'dataMatkul'])->name('dataMatkul');
            Route::post('/', [MatkulController::class, 'store'])->name('store');
            Route::get('/{matkul_id}', [MatkulController::class, 'show'])->name('show');
            Route::put('/{matkul_id}', [MatkulController::class, 'update'])->name('update');
            Route::delete('/{matkul_id}', [MatkulController::class, 'destroy'])->name('destroy');

            Route::prefix('{matkul_id}/materi')->name('materi.')->group(function () {
                Route::get('/', [MateriController::class, 'index'])->name('index');
                Route::post('/', [MateriController::class, 'store'])->name('store');
                Route::get('data', [MateriController::class, 'data'])->name('data');
                Route::get('/{materi_id}', [MateriController::class, 'show'])->name('show');
                Route::put('/{materi_id}', [MateriController::class, 'update'])->name('update');
                Route::delete('/{materi_id}', [MateriController::class, 'destroy'])->name('destroy');
            });
        });

        //? Prodi
        Route::get('prodi/data', [ProdiController::class, 'data'])->name('prodi.data');
        Route::post('prodi/neo-feeder', [ProdiController::class, 'storeNeoFeeder'])->name('prodi.storeNeoFeeder');
        Route::get('prodi/{prodi_id}/angkatan', [ProdiController::class, 'angkatan'])->name('prodi.angkatan');
        Route::get(
            'prodi/{prodi_id}/angkatan/{tahun_ajaran_id}',
            [ProdiController::class, 'angkatanDetail']
        )->name('prodi.angkatan.detail');
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
                Route::delete('/{id}', [PembayaranSemesterController::class, 'destroy'])->name('destroy');
            });

            //? Prodi - Pembayaran lainnya
            Route::prefix('pembayaran-lainnya')->name('pembayaran-lainnya.')->group(function () {
                Route::get('/jenis', [PembayaranLainnyaController::class, 'getJenis'])->name('getJenis');
                Route::get('/data', [PembayaranLainnyaController::class, 'data'])->name('data');
                Route::post('/', [PembayaranLainnyaController::class, 'store'])->name('store');
                Route::get('/{id}', [PembayaranLainnyaController::class, 'show'])->name('show');
                Route::put('/{id}', [PembayaranLainnyaController::class, 'update'])->name('update');
                Route::delete('/{id}', [PembayaranLainnyaController::class, 'destroy'])->name('destroy');
            });

            //? Prodi - Potongan
            Route::prefix('potongan')->name('potongan.')->group(function () {
                Route::get('/data', [PotonganController::class, 'data'])->name('data');
                Route::post('/', [PotonganController::class, 'store'])->name('store');
                Route::get('/{id}', [PotonganController::class, 'show'])->name('show');
                Route::put('/{id}', [PotonganController::class, 'update'])->name('update');
                Route::delete('/{id}', [PotonganController::class, 'destroy'])->name('destroy');
            });

            //? Prodi - MBKM
            Route::prefix('mbkm')->name('mbkm.')->group(function () {
                Route::get('/data', [MBKMController::class, 'data'])->name('data');
                Route::post('/', [MBKMController::class, 'store'])->name('store');
                Route::get('/{id}', [MBKMController::class, 'show'])->name('show');
                Route::get('/{id}/set', [MBKMController::class, 'set'])->name('set');
                Route::put('/{id}', [MBKMController::class, 'update'])->name('update');
                Route::delete('/{id}', [MBKMController::class, 'destroy'])->name('destroy');

                Route::prefix('{id}/mahasiswa')->name('mahasiswa.')->group(function () {
                    Route::post('/', [MBKMMahasiswaController::class, 'store'])->name('store');
                    Route::get('/get-mhs', [MBKMMahasiswaController::class, 'getMhs'])->name('get-mhs');
                    Route::get('/data', [MBKMMahasiswaController::class, 'data'])->name('data');
                    Route::get('/{mhs_id}', [MBKMMahasiswaController::class, 'show'])->name('show');
                    Route::put('/{mhs_id}', [MBKMMahasiswaController::class, 'update'])->name('update');
                    Route::delete('/{mhs_id}', [MBKMMahasiswaController::class, 'destroy'])->name('destroy');
                });

                Route::prefix('{id}/dosen-pembimbing')->name('dosen-pembimbing.')->group(function () {
                    Route::post('/', [MBKMDosenPembimbingController::class, 'store'])->name('store');
                    Route::get('/get-dosen', [MBKMDosenPembimbingController::class, 'getDosen'])->name('get-dosen');
                    Route::get('/data', [MBKMDosenPembimbingController::class, 'data'])->name('data');
                    Route::get('/{dosen_id}', [MBKMDosenPembimbingController::class, 'show'])->name('show');
                    Route::put('/{dosen_id}', [MBKMDosenPembimbingController::class, 'update'])->name('update');
                    Route::delete('/{dosen_id}', [MBKMDosenPembimbingController::class, 'destroy'])->name('destroy');
                });

                Route::prefix('{id}/dosen-penguji')->name('dosen-penguji.')->group(function () {
                    Route::post('/', [MBKMDosenPengujiController::class, 'store'])->name('store');
                    Route::get('/get-dosen', [MBKMDosenPengujiController::class, 'getDosen'])->name('get-dosen');
                    Route::get('/data', [MBKMDosenPengujiController::class, 'data'])->name('data');
                    Route::get('/{dosen_id}', [MBKMDosenPengujiController::class, 'show'])->name('show');
                    Route::put('/{dosen_id}', [MBKMDosenPengujiController::class, 'update'])->name('update');
                    Route::delete('/{dosen_id}', [MBKMDosenPengujiController::class, 'destroy'])->name('destroy');
                });

                Route::prefix('neo-feeder')->name('neo-feeder.')->group(function () {
                    Route::post('/', [MBKMNeoFeederController::class, 'store'])->name('store');
                    Route::get('/{mbkm_id}', [MBKMNeoFeederController::class, 'show'])->name('show');
                    Route::patch('/{mbkm_id}', [MBKMNeoFeederController::class, 'update'])->name('update');
                });
            });
        });

        //? Rombel
        Route::get('rombel/data', [RombelController::class, 'data'])->name('rombel.data');
        Route::get(
            'rombel/{rombel_id}/get-tahun-ajaran',
            [RombelController::class, 'getTahunAjaran']
        )->name('rombel.getTahunAjaran');
        Route::get('rombel/get-dosen-pa', [RombelController::class, 'getDosenPa'])->name('rombel.getDosenPa');
        Route::prefix('rombel/')->name('rombel.dosen-pa.')->group(function () {
            Route::get('{rombel_id}/dosen-pa', [RombelController::class, 'indexDosenPa'])->name('index');
            Route::post('{rombel_id}/dosen-pa', [RombelController::class, 'storeDosenPa'])->name('store');
            Route::get('{rombel_id}/dosen-pa/data', [RombelController::class, 'dataDosenPa'])->name('data');
            Route::get('{rombel_id}/dosen-pa/{tahun_masuk_id}', [RombelController::class, 'showDosenPa'])->name('show');
            Route::put('{rombel_id}/dosen-pa/{tahun_masuk_id}', [RombelController::class, 'updateDosenPa'])->name('update');
            Route::delete('{rombel_id}/dosen-pa/{tahun_masuk_id}', [RombelController::class, 'deleteDosenPa'])->name('destroy');
        });
        Route::resource('rombel', RombelController::class);
    });

    Route::prefix('rekap-perkuliahan')->name('rekap-perkuliahan.')->group(function () {
        Route::get('/', [MatkulRekapController::class, 'index'])->name('index');
        Route::get('/data', [MatkulRekapController::class, 'data'])->name('data');
        Route::post('/neo-feeder', [MatkulRekapController::class, 'storeNeoFeeder'])->name('storeNeoFeeder');
        Route::get('/{semester_id}/{tahun_matkul_id}/get-data', [MatkulRekapController::class, 'getData'])->name('getData');
        Route::get('/{semester_id}/{tahun_matkul_id}/get-dosen', [MatkulRekapController::class, 'getDosen'])->name('getDosen');
        Route::get('/{semester_id}/{tahun_matkul_id}/get-mhs', [MatkulRekapController::class, 'getMhs'])->name('getMhs');
        Route::get('/{semester_id}/{tahun_matkul_id}', [MatkulRekapController::class, 'show'])->name('show');
        Route::patch('/{tahun_matkul_id}', [MatkulRekapController::class, 'update'])->name('update');
        Route::patch('/{tahun_matkul_id}/neo-feeder', [MatkulRekapController::class, 'updateNeoFeeder'])->name('updateNeoFeeder');
    });

    Route::prefix('kelola-kuesioner')->name('kelola-kuesioner.')->group(function () {
        //? Template Kuesioner
        Route::get(
            'template/{id}/change-status',
            [KuesionerController::class, 'change_status']
        )->name('template.change-status');
        Route::get('template/data', [KuesionerController::class, 'data'])->name('template.data');
        Route::resource('template', KuesionerController::class);

        //? Response Kuesioner
        Route::prefix('response')->name('response.')->group(function () {
            Route::get('/', [ResponseKuesionerController::class, 'index'])->name('index');
            Route::get('/data', [ResponseKuesionerController::class, 'data'])->name('data');
            Route::get('/getSemester', [ResponseKuesionerController::class, 'getSemester'])->name('getSemester');
            Route::get('/getMatkul', [ResponseKuesionerController::class, 'getMatkul'])->name('getMatkul');
            Route::get('/{id}', [ResponseKuesionerController::class, 'show'])->name('show');
        });
    });

    Route::prefix('kelola-presensi')->name('kelola-presensi.')->group(function () {
        //? Whitelist IP
        Route::get('whitelist-ip/data', [WhitelistIPController::class, 'data'])->name('whitelist-ip.data');
        Route::get('whitelist-ip/get', [WhitelistIPController::class, 'get_ip'])->name('whitelist-ip.get-ip');
        Route::resource('whitelist-ip', WhitelistIPController::class);

        //? Presensi
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [KelolaPresensiController::class, 'index'])->name('index');
            Route::get(
                '/get-tahun-ajaran',
                [KelolaPresensiController::class, 'getTahunAjaran']
            )->name('getTahunAjaran');
            Route::get(
                '/get-pengawas',
                [KelolaPresensiController::class, 'getPengawas']
            )->name('getPengawas');
            Route::get(
                '/{tahun_ajaran_id}/{prodi_id}/get-semester',
                [KelolaPresensiController::class, 'getSemester']
            )->name('getSemester');
            Route::get(
                '/{tahun_ajahan_id}/{prodi_id}/get-matkul',
                [KelolaPresensiController::class, 'getMatkul']
            )->name('getMatkul');
            Route::get(
                '/{tahun_ajaran_id}/get-jadwal',
                [KelolaPresensiController::class, 'getJadwal']
            )->name('getJadwal');
            Route::get(
                '/{tahun_ajaran_id}',
                [KelolaPresensiController::class, 'show']
            )->name('show');
            Route::get(
                '/{tahun_ajaran_id}/{tahun_matkul_id}/get-materi',
                [KelolaPresensiController::class, 'getMateri']
            )->name('getMateri');
            Route::get(
                '/{tahun_ajaran_id}/{tahun_matkul_id}/total',
                [KelolaPresensiController::class, 'getTotalPelajaran']
            )->name('getTotalPelajaran');
            Route::get(
                '/{tahun_ajaran_id}/{tahun_matkul_id}/get-pengajar',
                [KelolaPresensiController::class, 'getPengajar']
            )->name('getPengajar');
            Route::get(
                '/{tahun_ajaran_id}/{tahun_matkul_id}/get-ujian',
                [KelolaPresensiController::class, 'getJenisUjian']
            )->name('getJenisUjian');
            Route::post(
                '/{tahun_ajaran_id}',
                [KelolaPresensiController::class, 'store']
            )->name('store');
            Route::get(
                '/{tahun_ajaran_id}/{jadwal_id}',
                [KelolaPresensiController::class, 'showJadwal']
            )->name('showJadwal');
            Route::put(
                '/{jadwal_id}',
                [KelolaPresensiController::class, 'updateJadwal']
            )->name('updateJadwal');
            Route::get(
                '/{tahun_ajaran_id}/{jadwal_id}/jadwal',
                [KelolaPresensiController::class, 'showJadwalEdit']
            )->name('showJadwalEdit');
            Route::put(
                '/{tahun_ajaran_id}/{jadwal_id}/jadwal',
                [KelolaPresensiController::class, 'updateJadwalEdit']
            )->name('updateJadwalEdit');
            Route::delete(
                '/{tahun_ajaran_id}/{jadwal_id}',
                [KelolaPresensiController::class, 'deleteJadwal']
            )->name('deleteJadwal');
            Route::put(
                '/{jadwal_id}/mulai',
                [KelolaPresensiController::class, 'mulaiJadwal']
            )->name('mulaiJadwal');
            Route::put(
                '/{jadwal_id}/selesai',
                [KelolaPresensiController::class, 'selesaiJadwal']
            )->name('selesaiJadwal');
            Route::get(
                '/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/get-presensi',
                [KelolaPresensiController::class, 'getPresensi']
            )->name('getPresensi');
            Route::get(
                '/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/{mhs_id}',
                [KelolaPresensiController::class, 'getPresensiMhs']
            )->name('getPresensiMhs');
            Route::put(
                '/{tahun_ajaran_id}/{jadwal_id}/{rombel_id}/{mhs_id}',
                [KelolaPresensiController::class, 'updatePresensiMhs']
            )->name('updatePresensiMhs');
        });

        //? Rekap Presensi
        Route::prefix('rekap')->name('rekap.')->group(function () {
            Route::get('/get-rombel', [RekapPresensiController::class, 'getRombel'])->name('getRombel');
            Route::prefix('{tahun_ajaran_id}')->group(function () {
                Route::get('/', [RekapPresensiController::class, 'index'])->name('index');
                Route::get('/get-presensi', [RekapPresensiController::class, 'getPresensi'])->name('getPresensi');
                Route::get('/get-matkul', [RekapPresensiController::class, 'getMatkul'])->name('getMatkul');
                Route::get('/get-semester', [RekapPresensiController::class, 'getSemester'])->name('getSemester');
                Route::get('/{tahun_matkul_id}', [RekapPresensiController::class, 'show'])->name('show');
            });
        });

        //? Berita Acara
        Route::prefix('berita-acara/{tahun_ajaran_id}')->name('berita-acara.')->group(function () {
            Route::get('/', [BeritaAcaraController::class, 'index'])->name('index');
            Route::get('/data', [BeritaAcaraController::class, 'data'])->name('data');
            Route::get('/{tahun_matkul_id}/{tahun_semester_id}/print', [BeritaAcaraController::class, 'print'])
                ->name('print');
        });
    });

    Route::prefix('kelola-nilai')->name('kelola-nilai.')->group(function () {
        Route::get(
            '/',
            [NilaiController::class, 'index']
        )->name('index');
        Route::get(
            '/dataTahunAjaran',
            [NilaiController::class, 'dataTahunAjaran']
        )->name('dataTahunAjaran');
        Route::get(
            '/{tahun_ajaran_id}',
            [NilaiController::class, 'show']
        )->name('show');
        Route::get(
            '/{tahun_ajaran_id}/get-matkul',
            [NilaiController::class, 'getMatkul']
        )->name('getMatkul');
        Route::post('/neo-feeder', [NilaiController::class, 'storeNeoFeeder'])->name('storeNeoFeeder');
        Route::get('/{tahun_ajaran_id}/getRombel', [NilaiController::class, 'getRombel'])->name('getRombel');
        Route::get(
            '/{tahun_semester_id}/{tahun_matkul_id}/{mhs_id}/nilai',
            [NilaiController::class, 'getNilai']
        )->name('getNilai');
        Route::put(
            '/{tahun_semester_id}/{tahun_matkul_id}/{mhs_id}/nilai',
            [NilaiController::class, 'store']
        )->name('store');
        Route::get(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}',
            [NilaiController::class, 'detailRombel']
        )->name('detailRombel');
        Route::get(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}/mhs',
            [NilaiController::class, 'dataMhs']
        )->name('dataMhs');
        Route::get(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}/get-data',
            [NilaiController::class, 'getDataNilai']
        )->name('getDataNilai');
        Route::patch(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}/update-neo-feeder',
            [NilaiController::class, 'updateNeoFeeder']
        )->name('updateNeoFeeder');
        Route::get(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}/download-template',
            [NilaiController::class, 'downloadTemplate']
        )->name('downloadTemplate');
        Route::post(
            '/{tahun_ajaran_id}/{rombel_id}/{tahun_semester_id}/{tahun_matkul_id}/download-template',
            [NilaiController::class, 'importNilai']
        )->name('importNilai');
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
            Route::patch('/{pembayaran_id}/revisi', [KelolaPembayaranController::class, 'revisi'])->name('revisi');
        });

        Route::get(
            'pembayaran-lainnya/data',
            [KelolaPembayaranLainnyaController::class, 'data']
        )->name('pembayaran-lainnya.data');
        Route::resource('pembayaran-lainnya', KelolaPembayaranLainnyaController::class);
    });

    Route::prefix('kelola-gaji')->name('kelola-gaji.')->group(function () {
        Route::get('/', [GajiController::class, 'index'])->name('index');
        Route::get('data', [GajiController::class, 'data'])->name('data');
        Route::post('/', [GajiController::class, 'store'])->name('store');
        Route::get('/{id}', [GajiController::class, 'show'])->name('show');
        Route::get('/{id}/export', [GajiController::class, 'export'])->name('export');
        Route::get('/{id}/generate-ulang', [GajiController::class, 'generateUlang'])->name('generateUlang');
        Route::patch('/{id}/publish', [GajiController::class, 'publish'])->name('publish');
        Route::patch('/{id}/unpublish', [GajiController::class, 'unpublish'])->name('unpublish');
        Route::get('/{id}/data', [GajiController::class, 'dataDetail'])->name('dataDetail');
        Route::get('/{id}/{user_id}/matkul', [GajiController::class, 'showMatkul'])->name('showMatkul');
        Route::get('/{id}/{user_id}/data-matkul', [GajiController::class, 'dataMatkul'])->name('dataMatkul');
        Route::delete('/{id}', [GajiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('gaji')->name('gaji.')->group(function () {
        Route::get('/', [PengajarGajiController::class, 'index'])->name('index');
        Route::get('data', [PengajarGajiController::class, 'data'])->name('data');
    });

    Route::get('setting/data', [SettingController::class, 'data'])->name('setting.data');
    Route::resource('setting', SettingController::class)->only('index', 'show', 'update');

    Route::prefix('verifikasi-krs')->name('verifikasi-krs.')->group(function () {
        Route::get('/', [KelolaKrsController::class, 'index'])->name('index');
        Route::get('/data', [KelolaKrsController::class, 'data'])->name('data');
        Route::get('/{krs_id}', [KelolaKrsController::class, 'show'])->name('show');
        Route::post('/{krs_id}', [KelolaKrsController::class, 'store'])->name('store');
        Route::patch('/{krs_id}/revisi', [KelolaKrsController::class, 'revisi'])->name('revisi');
    });

    Route::prefix('krs/{tahun_semester_id}')->name('krs.')->group(function () {
        Route::post('/ajukan/{mhs_id?}', [KrsController::class, 'ajukan'])->name('ajukan');
        Route::post('/simpan/{mhs_id?}', [ControllersKrsController::class, 'simpan'])->name('simpan');
        Route::patch('/update-lock/{mhs_id}', [KelolaKrsController::class, 'updateLock'])->name('updateLock');
        Route::get('/dataMatkul/{mhs_id?}', [ControllersKrsController::class, 'dataMatkul'])->name('dataMatkul');
        Route::get('/getMatkul/{mhs_id?}', [ControllersKrsController::class, 'getMatkul'])->name('getMatkul');
        Route::get('/getTotalSks/{mhs_id?}', [ControllersKrsController::class, 'getTotalSks'])->name('getTotalSks');
        Route::post('/{mhs_id?}', [ControllersKrsController::class, 'store'])->name('store');
        Route::delete('/{tahun_matkul_id}/{mhs_id?}', [ControllersKrsController::class, 'destroy'])->name('destroy');;
    });

    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('data/{mhs_id?}', [MahasiswaPembayaranController::class, 'data'])->name('data');
        Route::get('export', [MahasiswaPembayaranController::class, 'export'])->name('export');
        Route::get('/', [MahasiswaPembayaranController::class, 'index'])->name('index');

        Route::prefix('{type}/{id}')->group(function () {
            Route::middleware('pembayaran.mhs')->group(function () {
                Route::get('/create', [MahasiswaPembayaranController::class, 'create'])->name('create');
                Route::post('/', [MahasiswaPembayaranController::class, 'store'])->name('store');
                Route::get('/{pembayaran_id}/revisi', [MahasiswaPembayaranController::class, 'revisi'])->name('revisi');
                Route::patch(
                    '/{pembayaran_id}/revisi',
                    [MahasiswaPembayaranController::class, 'storeRevisi']
                )->name('storeRevisi');
                Route::get('/{pembayaran_id}/edit', [MahasiswaPembayaranController::class, 'edit'])->name('edit');
                Route::patch('/{pembayaran_id}', [MahasiswaPembayaranController::class, 'update'])->name('update');
                Route::delete('/{pembayaran_id}', [MahasiswaPembayaranController::class, 'destroy'])->name('destroy');
                Route::get('/{pembayaran_id}/print', [MahasiswaPembayaranController::class, 'print'])->name('print');
            });

            Route::get(
                '/dataPembayaran/{mhs_id?}',
                [MahasiswaPembayaranController::class, 'dataPembayaran']
            )->name('dataPembayaran');
            Route::get('/{mhs_id?}', [MahasiswaPembayaranController::class, 'show'])->name('show');
            Route::get(
                '/{pembayaran_id}/{mhs_id?}',
                [MahasiswaPembayaranController::class, 'showPembayaran']
            )->name('showPembayaran');
        });
    });

    Route::prefix('krs')->name('krs.')->group(function () {
        Route::get('/', [KrsController::class, 'index'])->name('index');
        Route::get('dataSemester/{mhs_id?}', [KrsController::class, 'dataSemester'])->name('dataSemester');
        Route::get('/{tahun_semester_id}/print', [KrsController::class, 'print'])->name('print');
        Route::get('/{tahun_semester_id}/{mhs_id?}', [KrsController::class, 'show'])->name('show');
        Route::patch('/{tahun_semester_id}/revisi', [KrsController::class, 'revisi'])->name('revisi');
    });

    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/', [MahasiswaPresensiController::class, 'index'])->name('index');
        Route::get('/data', [MahasiswaPresensiController::class, 'data'])->name('data');
        Route::post('/', [MahasiswaPresensiController::class, 'store'])->name('store');
    });

    Route::prefix('khs')->name('khs.')->group(function () {
        Route::get('/', [KhsController::class, 'index'])->name('index');
        Route::get('/dataSemester/{mhs_id?}', [KhsController::class, 'dataSemester'])->name('dataSemester');
        Route::get('/{tahun_semester_id}/print', [KhsController::class, 'print'])->name('print');
        Route::get('/{tahun_semester_id}/data/{mhs_id?}', [KhsController::class, 'data'])->name('data');
        Route::get('/{tahun_semester_id}/{mhs_id?}', [KhsController::class, 'show'])->name('show');
    });

    Route::prefix('transkip')->name('transkip.')->group(function () {
        Route::get('/', [TranskipController::class, 'index'])->name('index');
        Route::get('/data', [TranskipController::class, 'data'])->name('data');
        Route::get('/print', [TranskipController::class, 'print'])->name('print');
        Route::get('/export', [TranskipController::class, 'export'])->name('export');
    });

    Route::prefix('mbkm')->name('mbkm.')->group(function () {
        Route::get('/', [MahasiswaMBKMController::class, 'index'])->name('index');
        Route::get('/data', [MahasiswaMBKMController::class, 'data'])->name('data');
        Route::get('/{id}', [MahasiswaMBKMController::class, 'show'])->name('show');
        Route::get('/{id}/get-mhs', [MahasiswaMBKMController::class, 'getMhs'])->name('get-mhs');
        Route::get('/{id}/get-pembimbing', [MahasiswaMBKMController::class, 'getPembimbing'])->name('get-pembimbing');
        Route::get('/{id}/get-penguji', [MahasiswaMBKMController::class, 'getPenguji'])->name('get-penguji');
    });

    Route::prefix('bimbingan')->name('bimbingan.')->group(function () {
        Route::get('/', [BimbinganController::class, 'index'])->name('index');
        Route::get('/data/{mhs_id?}', [BimbinganController::class, 'data'])->name('data');
        Route::get('/{tahun_semester_id}/{mhs_id?}', [BimbinganController::class, 'show'])->name('show');
        Route::put(
            '/{tahun_semester_id}/{mhs_id?}',
            [BimbinganController::class, 'storeOrUpdate']
        )->name('storeOrUpdate');
    });

    Route::post('/kuesioner', [MahasiswaKuesionerController::class, 'store'])->name('kuesioner.store');

    Route::get('template-surat', [ControllersTemplateSuratController::class, 'index'])->name('template-surat.index');

    Route::prefix('neo-feeder')->name('neo-feeder.')->group(function () {
        Route::get('/{type}', [NeoFeederController::class, 'index'])->name('index');
        Route::post('/', [NeoFeederController::class, 'store'])->name('store');
        Route::get('/{type}/data', [NeoFeederController::class, 'data'])->name('data');
        Route::get('/{type}/get', [NeoFeederController::class, 'get'])->name('get');
        Route::patch('/{type}/non-active', [NeoFeederController::class, 'nonActive'])->name('nonActive');
    });

    Route::get('get-wilayah', [WilayahController::class, 'getWilayah'])->name('get-wilayah');
});

require __DIR__ . '/auth.php';
