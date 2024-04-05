@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('data-master.prodi.show', request('prodi_id')) }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Angkatan {{ request('tahun_ajaran_id') }}</h5>
                </div>
                <div class="card-body">
                    <div id="tab-main">
                        <ul class="nav nav-tabs" id="detail">
                            @can('view_semester')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <button data-bs-toggle="tab" data-bs-target="#semester"
                                        class="nav-link a-tab active">Semester</button>
                                </li>
                            @endcan
                            @can('view_kelola_pembayaran')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <button data-bs-toggle="tab" data-bs-target="#pembayaran-semester"
                                        class="nav-link a-tab">Pembayaran Semester</button>
                                </li>
                            @endcan
                            @can('view_pembayaran_lainnya')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <button data-bs-toggle="tab" class="nav-link a-tab"
                                        data-bs-target="#pembayaran-lainnya">Pembayaran Lainnya</button>
                                </li>
                            @endcan
                            @can('view_potongan')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <button data-bs-toggle="tab" class="nav-link a-tab"
                                        data-bs-target="#potongan">Potongan</button>
                                </li>
                            @endcan
                            @can('view_kelola_mbkm')
                                <li class="nav-item" style="white-space: nowrap;">
                                    <button data-bs-toggle="tab" class="nav-link a-tab" data-bs-target="#mbkm">MBKM</button>
                                </li>
                            @endcan
                        </ul>
                    </div>

                    <div class="tab-content py-4 px-1" id="detailContent">
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

                        @can('view_kelola_mbkm')
                            @include('data_master.prodi.angkatan.partials.mbkm')
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
