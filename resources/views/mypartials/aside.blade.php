<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="/dashboard" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2" style="text-transform: capitalize;">SPP</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1" style="overflow-y: auto;overflow-x: hidden">
    
        @can('view_kelola_pembayaran')
        <!-- Dashboard -->
        <li class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <a href="/dashboard" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        @endcan

        @can('view_tahun_ajaran')
        <li class="menu-item {{ Request::is('data-master*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Layouts">Data Master</div>
            </a>
    
            <ul class="menu-sub">
                <li class="menu-item {{ Request::is('data-master/tahun-ajaran*') ? 'active' : '' }}">
                    <a href="{{ route('data-master.tahun-ajaran.index') }}" class="menu-link">
                        <div data-i18n="Tahun Ajaran" class="text-capitalize">Tahun Ajaran</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::is('data-master/prodi*') ? 'active' : '' }}">
                    <a href="{{ route('data-master.prodi.index') }}" class="menu-link">
                        <div data-i18n="Prodi" class="text-capitalize">Prodi</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('view_users')
        <li class="menu-item {{ Request::is('users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Layouts">Users</div>
            </a>
    
            <ul class="menu-sub">
                @foreach (getRoleWithout(['admin']) as $role)
                    <li class="menu-item {{ Request::is('users/'. $role['name'] .'*') ? 'active' : '' }}">
                        <a href="{{ route('users.index', $role['name']) }}" class="menu-link">
                            <div data-i18n="{{ $role['name'] }}" class="text-capitalize">{{ $role['name'] }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
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

        @can('view_kelola_pembayaran')
        <li class="menu-item {{ Request::is('kelola/pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('kelola.pembayaran.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Analytics">Pembayaran</div>
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
    </ul>
</aside>