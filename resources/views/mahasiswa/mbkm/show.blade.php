@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('mbkm.index') }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">MBKM</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_anggota" class="form-label">Jenis Anggota</label>
                                <select class="form-select" name="jenis_anggota" id="jenis_anggota" disabled>
                                    <option value="">Pilih Jenis Anggota</option>
                                    <option value="0" {{ $data->jenis_anggota == 0 ? 'selected' : '' }}>Personal
                                    </option>
                                    <option value="1" {{ $data->jenis_anggota == 1 ? 'selected' : '' }}>Kelompok
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_aktivitas_id" class="form-label">Jenis Aktivitas</label>
                                <select class="form-select" name="jenis_aktivitas_id" id="jenis_aktivitas_id" disabled>
                                    <option value="">Pilih Jenis Aktivitas</option>
                                    @foreach ($jenisAktivitas as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $data->jenis_aktivitas_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tahun_semester_id" class="form-label">Semester</label>
                                <input type="text" class="form-control" id="semester" name="semester" disabled
                                value="{{ $data->semester }}">
                            </div>
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul</label>
                                <input type="text" class="form-control" id="judul" name="judul" disabled
                                    value="{{ $data->judul }}">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" disabled
                                    value="{{ $data->tanggal_mulai }}">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                                    disabled value="{{ $data->tanggal_selesai }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="textarea-mbkm" rows="3" name="ket" disabled>{{ $data->ket }}
                                </textarea>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">lokasi</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" disabled
                                    value="{{ $data->lokasi }}">
                            </div>
                            <div class="mb-3">
                                <label for="sk_tugas" class="form-label">SK Tugas</label>
                                <input type="text" class="form-control" id="sk_tugas" name="sk_tugas" disabled
                                    value="{{ $data->sk_tugas }}">
                            </div>
                            <div class="mb-3">
                                <label for="tgl_sk_tugas" class="form-label">Tanggal SK Tugas</label>
                                <input type="date" class="form-control" id="tgl_sk_tugas" name="tgl_sk_tugas" disabled
                                    value="{{ $data->tgl_sk_tugas }}">
                            </div>

                        </div>
                    </div>
                    <ul class="nav nav-tabs" id="profile">
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab active"
                                data-bs-target="#dosen-pembimbing">Dosen Pembimbing</button>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                                data-bs-target="#dosen-penguji">Dosen Penguji</button>
                        </li>
                        @if (Auth::user()->hasRole('dosen'))
                        <li class="nav-item" style="white-space: nowrap;">
                            <button type="button" data-bs-toggle="tab" class="nav-link a-tab"
                                data-bs-target="#mahasiswa">Mahasiswa</button>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content py-4 px-1" id="detail">
                        @include('mahasiswa.mbkm.partials.dosen_pembimbing')
                        @include('mahasiswa.mbkm.partials.dosen_penguji')
                        @include('mahasiswa.mbkm.partials.mhs')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
