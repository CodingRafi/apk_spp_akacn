@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('data-master.prodi.show', request('prodi_id')) }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Angkatan {{ request('tahun_ajaran_id') }}</h5>
                </div>
                <div class="card-body">
                    <div id="tab-main">
                        <ul class="nav nav-tabs">
                            @can('view_semester')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link a-tab active" href="#semester">Semester</a>
                                </li>
                            @endcan
                            @can('view_kelola_pembayaran')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link a-tab" href="#pembayaran-semester">Pembayaran Semester</a>
                                </li>
                            @endcan
                            @can('view_pembayaran_lainnya')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link a-tab" href="#pembayaran-lainnya">Pembayaran Lainnya</a>
                                </li>
                            @endcan
                            @can('view_potongan')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <a class="nav-link a-tab" href="#potongan">Potongan</a>
                                </li>
                            @endcan
                        </ul>
                    </div>

                    <div class="tab-content py-4 px-1">
                        @can('view_semester')
                            @include('data_master.prodi.angkatan.partials.semester')
                        @endcan

                        @can('view_kelola_pembayaran')
                            @include('data_master.prodi.angkatan.partials.pembayaran_semester')
                        @endcan

                        @can('view_pembayaran_lainnya')
                            @include('data_master.prodi.angkatan.partials.pembayaran_lainnya')
                        @endcan

                        @can('view_potongan')
                            @include('data_master.prodi.angkatan.partials.potongan')
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
