@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-users.index', ['role' => 'mahasiswa']) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Detail Mahasiswa</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="detail">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profil"
                                type="button">Profil</button>
                        </li>
                        @can('view_kelola_pembayaran')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pembayaran"
                                    type="button">Pembayaran</button>
                            </li>
                        @endcan
                        @can('view_kelola_krs')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#krs" type="button">
                                    KRS</button>
                            </li>
                        @endcan
                        @can('view_kelola_presensi')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#presensi"
                                    type="button">Presensi</button>
                            </li>
                        @endcan
                        @can('view_kelola_nilai')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#khs"
                                    type="button">KHS</button>
                            </li>
                        @endcan
                        @can('view_bimbingan')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bimbingan_akademik"
                                type="button">Bimbingan Akademik</button>
                        </li>
                        @endcan
                    </ul>

                    <hr>

                    <div class="tab-content p-0 mt-3" id="detailContent">
                        <div class="tab-pane fade show active" id="profil" role="tabpanel" tabindex="0">
                            @include('users.mahasiswa.form', [
                                'disabled' => true,
                                'role' == 'mahasiswa',
                            ])
                        </div>
                        @can('view_kelola_pembayaran')
                            <div class="tab-pane fade show" id="pembayaran" role="tabpanel" tabindex="0">
                                <h5 class="card-title">Pembayaran</h5>
                                @include('mahasiswa.pembayaran.table', [
                                    'mhs_id' => $data->id,
                                ])

                                <h5 class="card-title mt-3">Pembayaran Tambahan</h5>
                                @include('users.mahasiswa.pembayaran_tambahan.index', [
                                    'mhs_id' => $data->id,
                                ])

                                <h5 class="card-title mt-3">Potongan</h5>
                                @include('users.mahasiswa.potongan.index', [
                                    'mhs_id' => $data->id,
                                ])
                            </div>
                        @endcan
                        @can('view_kelola_krs')
                            <div class="tab-pane fade show" id="krs" role="tabpanel" tabindex="0">
                                @include('mahasiswa.krs.table', [
                                    'mhs_id' => $data->id,
                                ])
                            </div>
                        @endcan
                        @can('view_kelola_presensi')
                            <div class="tab-pane fade show" id="presensi" role="tabpanel" tabindex="0">
                                @include('mahasiswa.presensi.table', [
                                    'mhs_id' => $data->id,
                                ])
                            </div>
                        @endcan
                        @can('view_kelola_nilai')
                            <div class="tab-pane fade show" id="khs" role="tabpanel" tabindex="0">
                                @include('mahasiswa.khs.table', [
                                    'mhs_id' => $data->id,
                                ])
                            </div>
                        @endcan
                        @can('view_bimbingan')
                            <div class="tab-pane fade show" id="bimbingan_akademik" role="tabpanel" tabindex="0">
                                @include('mahasiswa.bimbingan.table', [
                                    'mhs_id' => $data->id,
                                ])
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
