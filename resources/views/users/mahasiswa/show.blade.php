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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pembayaran"
                                type="button">Pembayaran</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#krs" type="button">KRS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#presensi"
                                type="button">Presensi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nilai"
                                type="button">Nilai</button>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content p-0 mt-3" id="detailContent">
                        <div class="tab-pane fade show active" id="profil" role="tabpanel" tabindex="0">
                            @include('users.mahasiswa.form', [
                                'disabled' => true,
                                'role' == 'mahasiswa',
                            ])
                        </div>
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
                        <div class="tab-pane fade show" id="krs" role="tabpanel" tabindex="0">
                            @include('mahasiswa.krs.table', [
                                'mhs_id' => $data->id,
                            ])
                        </div>
                        <div class="tab-pane fade show" id="presensi" role="tabpanel" tabindex="0">
                            @include('mahasiswa.presensi.table', [
                                'mhs_id' => $data->id,
                            ])
                        </div>
                        <div class="tab-pane fade show" id="nilai" role="tabpanel" tabindex="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
