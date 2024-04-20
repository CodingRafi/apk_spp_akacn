@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('data-master.prodi.angkatan.detail', ['prodi_id' => request('prodi_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Detail MBKM</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="profile">
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link active a-tab"
                                data-bs-target="#mahasiswa">Mahasiswa</button>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                                data-bs-target="#dosen-pembimbing">Dosen Pembimbing</button>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                                data-bs-target="#dosen-penguji">Dosen Penguji</button>
                        </li>
                    </ul>
                    <div class="tab-content py-4 px-1" id="detail">
                        @include('data_master.prodi.angkatan.mbkm.partials.mhs')
                        @include('data_master.prodi.angkatan.mbkm.partials.dosen_pembimbing')
                        @include('data_master.prodi.angkatan.mbkm.partials.dosen_penguji')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $(".select2-mahasiswa").select2({
                dropdownParent: $("#mahasiswaModal")
            });

            $(".select2-kategori-kegiatan-pembimbing, .select2-dosen-pembimbing").select2({
                dropdownParent: $("#dosenPembimbingModal")
            });

            $(".select2-kategori-kegiatan-penguji, .select2-dosen-penguji").select2({
                dropdownParent: $("#dosenPengujiModal")
            });
        })
    </script>
@endpush
