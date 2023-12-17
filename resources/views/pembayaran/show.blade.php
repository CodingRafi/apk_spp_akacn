@extends('mylayouts.main')

@section('container')
@php
$data = $semester->tahun_ajaran()->where('tahun_ajaran_id', $mhs->tahun_ajaran_id)->first();
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('pembayaran.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Pembayaran {{ $semester->nama }}</h5>
                </div>
                @if ($data && $data->pivot->publish)
                <a href="{{ route('pembayaran.create', $semester->id) }}"
                    class="btn btn-primary text-capitalize">Bayar</a>
                @endif
            </div>
            <div class="card-body">
                @if ($data && $data->pivot->publish)
                <div class="container-fluid p-0">
                    <h5>Detail Pembayaran</h5>
                    <p>Biaya: <strong>{{ formatRupiah($data->pivot->nominal) }}</strong></p>
                    <p>Sudah dibayar: <strong>{{ formatRupiah($sudah_dibayar) }}</strong></p>
                    <p>Kekurangan: <strong>{{ formatRupiah(((($data->pivot->nominal - $sudah_dibayar) < 0) ? 0 : $data->pivot->nominal - $sudah_dibayar)) }}</strong></p>
                    {!! $data->pivot->ket !!}
                </div>
                <hr>
                @else
                <div class="alert alert-primary" role="alert">
                    Maaf belum ada pembayaran
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nominal</th>
                                <th>Tanggal Bayar</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function(){
        $('.table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route("pembayaran.dataPembayaran", request('semester_id')) }}',
            columns: [
                        { "data": "DT_RowIndex" },
                        { "data": "nominal" },
                        { "data": "tgl_bayar" },
                        { "data": "bukti" },
                        { "data": "status" },
                        { "data": "options" }
                    ],
            pageLength: 25,
            responsive: true,
        });
    });
</script>
@endpush