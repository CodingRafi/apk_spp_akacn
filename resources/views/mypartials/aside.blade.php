<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="/dashboard" class="app-brand-link">
            <span
                class="app-brand-text demo menu-text fw-bolder ms-2 d-flex justify-content-center align-items-center text-capitalize"
                style="gap: .5rem;">
                <img src="{{ asset('image/logo.png') }}" alt="" style="width: 2.9rem">
                AKACN
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1" style="overflow-y: auto;overflow-x: hidden">

        <li class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <a href="/dashboard" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        @canany(['view_neo_feeder'])
            <li class="menu-item {{ Request::is('neo-feeder*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-archive"></i>
                    <div data-i18n="Layouts">Data Master Neo Feeder</div>
                </a>

                @php
                    $type = [
                        'agama',
                        'jenis_tinggal',
                        'alat_transportasi',
                        'jenjang',
                        'kewarganegaraan',
                        'lembaga_pengangkat',
                        'pekerjaan',
                        'penghasilan',
                        'pangkat_golongan',
                        'jenis_pembiayaan',
                        'jenis_daftar',
                        'jalur_masuk',
                        'jenis_keluar',
                        'wilayah',
                    ];
                @endphp

                <ul class="menu-sub">
                    @foreach ($type as $item)
                        <li class="menu-item {{ Request::is('neo-feeder/' . $item . '*') ? 'active' : '' }}">
                            <a href="{{ route('neo-feeder.index', ['type' => $item]) }}" class="menu-link">
                                <div data-i18n="{{ $item }}" class="text-capitalize">
                                    {{ str_replace('_', ' ', $item) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endcanany

        @canany(['view_tahun_ajaran', 'view_prodi', 'view_rombel', 'view_kurikulum', 'view_kuesioner', 'view_ruang',
            'view_kelola_template_surat', 'view_kelola_mutu'])
            <li class="menu-item {{ Request::is('data-master*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-archive"></i>
                    <div data-i18n="Layouts">Data Master</div>
                </a>

                <ul class="menu-sub">
                    @can('view_tahun_ajaran')
                        <li class="menu-item {{ Request::is('data-master/tahun-ajaran*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.tahun-ajaran.index') }}" class="menu-link">
                                <div data-i18n="Tahun Ajaran" class="text-capitalize">Tahun Ajaran</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_prodi')
                        <li class="menu-item {{ Request::is('data-master/prodi*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.prodi.index') }}" class="menu-link">
                                <div data-i18n="Prodi" class="text-capitalize">Prodi</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_rombel')
                        <li class="menu-item {{ Request::is('data-master/rombel*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.rombel.index') }}" class="menu-link">
                                <div data-i18n="rombel">Rombel</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_kurikulum')
                        <li class="menu-item {{ Request::is('data-master/kurikulum*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.kurikulum.index') }}" class="menu-link">
                                <div data-i18n="kurikulum">Kurikulum</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_matkul')
                        <li class="menu-item {{ Request::is('data-master/mata-kuliah*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.mata-kuliah.index') }}" class="menu-link">
                                <div data-i18n="mata-kuliah">Mata Kuliah</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_ruang')
                        <li class="menu-item {{ Request::is('data-master/ruang*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.ruang.index') }}" class="menu-link">
                                <div data-i18n="ruang">Ruang</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_jenis_kelas')
                        <li class="menu-item {{ Request::is('data-master/jenis-kelas*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.jenis-kelas.index') }}" class="menu-link">
                                <div data-i18n="jenis-kelas">Jenis Kelas</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_mutu')
                        <li class="menu-item {{ Request::is('data-master/mutu*') ? 'active' : '' }}">
                            <a href="{{ route('data-master.mutu.index') }}" class="menu-link">
                                <div data-i18n="mutu">Mutu</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_kelola_template_surat')
                        @if (getRole()->name == 'admin')
                            <li class="menu-item {{ Request::is('data-master/template-surat*') ? 'active' : '' }}">
                                <a href="{{ route('data-master.template-surat.index') }}" class="menu-link">
                                    <div data-i18n="template-surat">Template Surat</div>
                                </a>
                            </li>
                        @endif
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['view_kuesioner'])
            <li class="menu-item {{ Request::is('kelola-kuesioner*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-archive"></i>
                    <div data-i18n="Layouts">Kelola Kuesioner</div>
                </a>

                <ul class="menu-sub">
                    @can('view_kuesioner')
                        <li class="menu-item {{ Request::is('kelola-kuesioner/template*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-kuesioner.template.index') }}" class="menu-link">
                                <div data-i18n="Template" class="text-capitalize">Template</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canAny(['view_whitelist_ip', 'view_kelola_presensi'])
            <li class="menu-item {{ Request::is('kelola-presensi*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-archive"></i>
                    <div data-i18n="Layouts">Kelola Presensi</div>
                </a>

                <ul class="menu-sub">
                    @can('view_whitelist_ip')
                        <li class="menu-item {{ Request::is('kelola-presensi/whitelist-ip*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-presensi.whitelist-ip.index') }}" class="menu-link">
                                <div data-i18n="whitelist-ip">Whitelist IP</div>
                            </a>
                        </li>
                    @endcan
                    @can('view_kelola_presensi')
                        <li class="menu-item {{ Request::is('kelola-presensi/presensi*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-presensi.presensi.index') }}" class="menu-link">
                                <div data-i18n="presensi">Presensi</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanAny

        @canany(['view_potongan', 'view_kelola_pembayaran', 'view_pembayaran_lainnya'])
            <li class="menu-item {{ Request::is('kelola-pembayaran*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-archive"></i>
                    <div data-i18n="Layouts">Kelola Pembayaran</div>
                </a>

                <ul class="menu-sub">
                    @can('view_potongan')
                        <li class="menu-item {{ Request::is('kelola-pembayaran/potongan*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-pembayaran.potongan.index') }}" class="menu-link">
                                <div data-i18n="potongan" class="text-capitalize">Potongan Biaya</div>
                            </a>
                        </li>
                    @endcan

                    @can('view_pembayaran_lainnya')
                        <li class="menu-item {{ Request::is('kelola-pembayaran/pembayaran-lainnya*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-pembayaran.pembayaran-lainnya.index') }}" class="menu-link">
                                <div data-i18n="pembayaran-lainnya" class="text-capitalize">Pembayaran Lainnya</div>
                            </a>
                        </li>
                    @endcan

                    @can('view_kelola_pembayaran')
                        <li class="menu-item {{ Request::is('kelola-pembayaran/verifikasi-pembayaran*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-pembayaran.pembayaran.index') }}" class="menu-link">
                                <div data-i18n="pembayaran" class="text-capitalize">Verifikasi Pembayaran</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('view_users')
            <li class="menu-item {{ Request::is('kelola-users*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Layouts">Kelola Users</div>
                </a>

                <ul class="menu-sub">
                    @php
                        if (Auth::user()->hasRole('dosen')) {
                            $without = ['dosen', 'petugas', 'asdos', 'admin'];
                        } else {
                            $without = ['admin'];
                        }
                    @endphp
                    @foreach (getRoleWithout($without) as $role)
                        <li class="menu-item {{ Request::is('kelola-users/' . $role['name'] . '*') ? 'active' : '' }}">
                            <a href="{{ route('kelola-users.index', ['role' => $role['name']]) }}" class="menu-link">
                                <div data-i18n="{{ $role['name'] }}" class="text-capitalize">{{ $role['name'] }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endcan

        @can('view_kelola_krs')
            <li class="menu-item {{ Request::is('verifikasi-krs*') ? 'active' : '' }}">
                <a href="{{ route('verifikasi-krs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Verifikasi KRS</div>
                </a>
            </li>
        @endcan

        @can('view_roles')
            <li class="menu-item {{ Request::is('roles*') ? 'active' : '' }}">
                <a href="{{ route('roles.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                    <div data-i18n="Analytics">Roles</div>
                </a>
            </li>
        @endcan

        @can('view_kelola_nilai')
            <li class="menu-item {{ Request::is('kelola-nilai*') ? 'active' : '' }}">
                <a href="{{ route('kelola-nilai.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Kelola Nilai</div>
                </a>
            </li>
        @endcan

        @can('view_pembayaran')
            <li class="menu-item {{ Request::is('pembayaran*') ? 'active' : '' }}">
                <a href="{{ route('pembayaran.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Pembayaran</div>
                </a>
            </li>
        @endcan

        @can('view_krs')
            <li class="menu-item {{ Request::is('krs*') ? 'active' : '' }}">
                <a href="{{ route('krs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">KRS</div>
                </a>
            </li>
        @endcan

        @can('view_presensi')
            <li class="menu-item {{ Request::is('presensi*') ? 'active' : '' }}">
                <a href="{{ route('presensi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Presensi</div>
                </a>
            </li>
        @endcan

        @can('view_kelola_gaji')
            <li class="menu-item {{ Request::is('kelola-gaji*') ? 'active' : '' }}">
                <a href="{{ route('kelola-gaji.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Kelola Gaji</div>
                </a>
            </li>
        @endcan

        @can('view_setting')
            <li class="menu-item {{ Request::is('setting*') ? 'active' : '' }}">
                <a href="{{ route('setting.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Setting</div>
                </a>
            </li>
        @endcan

        @can('view_khs')
            <li class="menu-item {{ Request::is('khs*') ? 'active' : '' }}">
                <a href="{{ route('khs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Kartu Hasil Studi</div>
                </a>
            </li>
        @endcan

        @can('view_transkrip')
            <li class="menu-item {{ Request::is('transkip*') ? 'active' : '' }}">
                <a href="{{ route('transkip.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Transkip Nilai</div>
                </a>
            </li>
        @endcan

        @if (Auth::user()->hasRole('mahasiswa'))
            <li class="menu-item {{ Request::is('bimbingan*') ? 'active' : '' }}">
                <a href="{{ route('bimbingan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div data-i18n="Analytics">Bimbingan Akademik</div>
                </a>
            </li>
        @endif

        @can('view_template_surat')
            @if (getRole()->name != 'admin')
                <li class="menu-item {{ Request::is('template-surat*') ? 'active' : '' }}">
                    <a href="{{ route('template-surat.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Analytics">Template Surat</div>
                    </a>
                </li>
            @endif
        @endcan
    </ul>
</aside>
