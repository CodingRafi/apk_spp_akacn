@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('kelola-pembayaran.pembayaran.index') }}"><i
                                    class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">Detail Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            @if ($data->status != 'pengajuan')
                                @if ($data->status == 'diterima')
                                    <div class="alert alert-primary" role="alert">
                                        Pembayaran ini telah diterima
                                    </div>
                                @else
                                    <div class="alert alert-danger" role="alert">
                                        Pembayaran ini telah ditolak
                                    </div>
                                @endif
                            @endif
                            <div class="container-fluid border pt-3 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama</label>
                                            <input class="form-control" type="text" value="{{ $data->mahasiswa->name }}"
                                                id="name" disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label for="nim" class="form-label">NIM</label>
                                            <input class="form-control" type="text"
                                                value="{{ $data->mahasiswa->login_key }}" id="nim" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prodi" class="form-label">Program Studi</label>
                                            <input class="form-control" type="text"
                                                value="{{ $data->mahasiswa->mahasiswa->prodi->nama }}" id="prodi"
                                                disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label for="rombel" class="form-label">Rombel</label>
                                            <input class="form-control" type="text"
                                                value="{{ $data->mahasiswa->mahasiswa->rombel->nama }}" id="rombel"
                                                disabled />
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_pembayaran" class="form-label">Pembayaran</label>
                                    <input class="form-control" type="text"
                                        value="{{ $data->type->nama }}" disabled />
                                </div>
                                <div class="mb-3">
                                    <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                                    <input class="form-control @error('tgl_bayar') is-invalid @enderror" type="date"
                                        value="{{ isset($data) ? $data->tgl_bayar : old('tgl_bayar') }}" id="tgl_bayar"
                                        name="tgl_bayar" disabled />
                                </div>
                                <div class="mb-3">
                                    <label for="nominal" class="form-label">Nominal</label>
                                    <input class="form-control @error('nominal') is-invalid @enderror" type="number"
                                        value="{{ isset($data) ? $data->nominal : old('nominal') }}" id="nominal"
                                        name="nominal" disabled />
                                </div>
                                <div class="mb-3">
                                    <label for="bukti" class="form-label">Bukti Pembayaran</label>
                                    <div class="d-flex justify-content-between" style="gap: 1rem;">
                                        <a href="{{ asset('storage/' . $data->bukti) }}" class="btn btn-primary"
                                            target="_blank">Lihat</a>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="ket_mhs" class="form-label">Keterangan Mahasiswa</label>
                                    <p>
                                        {{ $data->ket_mhs ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <hr>
                            @if ($data->status == 'pengajuan')
                                <form action="{{ route('kelola-pembayaran.pembayaran.store', $data->id) }}"
                                    class="form-verify" method="post">
                                    @csrf
                                    <input type="hidden" name="status">
                                    <input type="hidden" name="revisi">
                                    <div class="mb-3">
                                        <label for="ket_verify" class="form-label">Komentar</label>
                                        <textarea class="form-control @error('ket_verify') is-invalid @enderror" id="textarea-tinymce" rows="3"
                                            name="ket_verify">{{ isset($data) ? $data->ket_verify : old('ket_verify') }}</textarea>
                                        @error('ket_verify')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="d-flex" style="gap: 1rem;">
                                        <button class="btn btn-verify btn-primary" data-status="diterima"
                                            type="button">Diterima</button>
                                        <button class="btn btn-verify btn-danger" data-status="ditolak"
                                            type="button">Ditolak</button>
                                    </div>
                                </form>
                            @else
                                <div class="mb-3">
                                    <label for="ket_mhs" class="form-label">Keterangan Verifikasi</label>
                                    {!! $data->ket_verify ?? '<br>-' !!}
                                </div>
                                <div class="mb-3">
                                    <label for="ket_mhs" class="form-label">Revisi : </label>
                                    {{ $data->revisi ? 'Ya' : 'Tidak' }}
                                </div>
                                @if ($data->verify_id == Auth::user()->id)
                                    <div class="d-flex" style="gap: 1rem;">
                                        <a class="btn btn-revisi btn-warning"
                                            href="{{ route('kelola.pembayaran.revisi', $data->id) }}" type="button"
                                            onclick="return confirm('Apakah anda yakin ingin?')">Revisi</a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('.btn-verify').on('click', function() {
            let status = $(this).data('status');
            $('input[name=status]').val(status);
            if (status == 'diterima') {
                $('.form-verify').submit();
            } else {
                Swal.fire({
                    title: 'Apakah anda yakin akan menolak pembayaran ini?',
                    text: 'Klik "Ya" jika setuju, klik "Tidak" jika tidak setuju',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Apakah mahasiswa boleh revisi?',
                            text: 'Klik "Ya" jika boleh, klik "Tidak" jika tidak boleh',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Tidak'
                        }).then((result) => {
                            $('input[name=revisi]').val(result.isConfirmed);
                            $('.form-verify').submit();
                        })
                    }
                })
            }
        })
    </script>
@endpush
