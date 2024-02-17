@extends('mylayouts.main')

@section('container')
@php
$page = isset($page) ? $page : 'form';
$revisi = isset($revisi) ? $revisi : false;
@endphp
<div class="content-wrapper">

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <form
                action="{{ isset($data) ? ($revisi ? route('pembayaran.storeRevisi', ['semester_id' => $semester->id, 'pembayaran_id' => $data->id]) : route('pembayaran.update', ['semester_id' => $semester->id, 'pembayaran_id' => $data->id])) : route('pembayaran.store', request('semester_id')) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($data))
                @method('patch')
                @endif
                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center ">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('pembayaran.show', $semester->id) }}"><i
                                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                    <h5 class="text-capitalize mb-0">Pembayaran {{ $semester->prodi->nama }} | {{
                                        $semester->nama }}</h5>
                                </div>
                                @if (isset($data) && $data->status != 'pengajuan' && $data->revisi == '1' && $page !=
                                'form')
                                <a href="{{ route('pembayaran.revisi', ['semester_id' => $semester->id, 'pembayaran_id' => $data->id]) }}"
                                    class="btn btn-warning" onclick="return confirm('Apakah anda yakin?')">Revisi</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if (isset($data))
                            @if ($data->status != 'pengajuan' && $page != 'form')
                            @if ($data->status == 'diterima')
                            <div class="alert alert-primary" role="alert">
                                Pembayaran ini telah diterima
                                @if ($data->ket_verify)
                                <hr>
                                {!! $data->ket_verify !!}
                                @endif
                            </div>
                            @else
                            <div class="alert alert-danger" role="alert">
                                Pembayaran ini telah ditolak
                                @if ($data->ket_verify)
                                <hr>
                                {!! $data->ket_verify !!}
                                @endif
                            </div>
                            @endif
                            @else
                            <div class="alert alert-info" role="alert">
                                Pembayaran ini sedang diajukan
                            </div>
                            @endif
                            @endif
                            <div class="mb-3">
                                <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                                <input class="form-control @error('tgl_bayar') is-invalid @enderror" type="date"
                                    value="{{ isset($data) ? $data->tgl_bayar : old('tgl_bayar') }}" id="tgl_bayar"
                                    name="tgl_bayar" {{ $page=='show' ? 'disabled' : '' }} />
                                @error('tgl_bayar')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input class="form-control @error('nominal') is-invalid @enderror" type="number"
                                    value="{{ isset($data) ? $data->nominal : old('nominal') }}" id="nominal"
                                    name="nominal" {{ $page=='show' ? 'disabled' : '' }} />
                                @error('nominal')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="bukti" class="form-label">Bukti Pembayaran</label>
                                <div class="d-flex justify-content-between" style="gap: 1rem;">
                                    @if ($page == 'form')
                                    <input class="form-control @error('bukti') is-invalid @enderror" type="file"
                                        value="{{ isset($data) ? $data->bukti : old('bukti') }}" id="bukti" name="bukti"
                                        accept="image/*" />
                                    @endif
                                    @if (isset($data))
                                    <a href="{{ asset('storage/' . $data->bukti) }}" class="btn btn-primary"
                                        target="_blank">Lihat</a>
                                    @endif
                                </div>
                                @error('bukti')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ket_mhs" class="form-label">Keterangan</label>
                                @if ($page == 'form')
                                <textarea class="form-control @error('ket_mhs') is-invalid @enderror" id="textarea-tinymce"
                                    rows="3"
                                    name="ket_mhs">{{ isset($data) ? $data->ket_mhs : old('ket_mhs') }}</textarea>
                                @else
                                {!! $data->ket_mhs !!}
                                @endif
                                @error('ket_mhs')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @if ($page == 'form')
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection