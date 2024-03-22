    @extends('mylayouts.main')

    @section('container')
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if (Auth::user()->hasRole('mahasiswa'))
                                <a href="{{ route('pembayaran.index') }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @else
                                <a href="{{ route('kelola-users.mahasiswa.show', $mhs_id) }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @endif
                            <h5 class="text-capitalize mb-0">Pembayaran</h5>
                        </div>
                        @can('add_pembayaran')
                            @if ($data && $data->publish)
                                <a href="{{ route('pembayaran.create', ['type' => request('type'), 'id' => request('id')]) }}"
                                    class="btn btn-primary text-capitalize">Bayar</a>
                            @endif
                        @endcan
                    </div>
                    <div class="card-body">
                        @if ($data && $data->publish)
                            <div class="row mb-2">
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    Tagihan
                                                </h5>
                                                <button class="bg-transparent p-0 border-0 text-secondary"
                                                    data-bs-toggle="modal" data-bs-target="#tagihan">Detail</button>
                                            </div>
                                            <p class="card-text">{{ formatRupiah($data->harus) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    Potongan
                                                </h5>
                                                <button class="bg-transparent p-0 border-0 text-secondary"
                                                    data-bs-toggle="modal" data-bs-target="#potongan">Detail</button>
                                            </div>
                                            <p class="card-text">{{ formatRupiah($data->potongan) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    Biaya Tambahan
                                                </h5>
                                                <button class="bg-transparent p-0 border-0 text-secondary"
                                                    data-bs-toggle="modal" data-bs-target="#tambahan">Detail</button>
                                            </div>
                                            <p class="card-text">{{ formatRupiah($data->tambahan) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Sudah dibayar</h5>
                                            <p class="card-text">{{ formatRupiah($data->total_pembayaran) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Belum dibayar</h5>
                                            <p class="card-text">{{ formatRupiah($data->sisa) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-primary" role="alert">
                                Maaf belum ada pembayaran
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-pembayaran">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nominal</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Bukti</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tagihan" tabindex="-1" aria-labelledby="tagihanLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="tagihanLabel">Detail Tagihan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {!! $data->ket !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="potongan" tabindex="-1" aria-labelledby="potonganLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="potonganLabel">Detail Potongan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table w-100">
                            <thead>
                                <tr>
                                    <th class="col-md-2">Nama</th>
                                    <th class="col-md-2">Nominal</th>
                                    <th class="col-md-8">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($potongan as $item)
                                    <tr>
                                        <td class="col-md-2">{{ $item->nama }}</td>
                                        <td class="col-md-2">{{ formatRupiah($item->nominal) }}</td>
                                        <td class="col-md-8">{{ $item->ket }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tambahan" tabindex="-1" aria-labelledby="tambahanLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="tambahanLabel">Detail Tambahan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table w-100">
                            <thead>
                                <tr>
                                    <th class="col-md-2">Nama</th>
                                    <th class="col-md-2">Untuk</th>
                                    <th class="col-md-2">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tambahan as $item)
                                    <tr>
                                        <td class="col-md-2">{{ $item->nama }}</td>
                                        <td class="col-md-2">{{ $item->semester ?? $item->lainnya }}</td>
                                        <td class="col-md-2">{{ formatRupiah($item->nominal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('js')
        <script>
            $(document).ready(function() {
                $('.table-pembayaran').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('pembayaran.dataPembayaran', ['type' => request('type'), 'id' => request('id'), 'mhs_id' => $mhs_id]) }}',
                    columns: [{
                            "data": "DT_RowIndex"
                        },
                        {
                            "data": "nominal"
                        },
                        {
                            "data": "tgl_bayar"
                        },
                        {
                            "data": "bukti"
                        },
                        {
                            "data": "status"
                        },
                        {
                            "data": "options"
                        }
                    ],
                    pageLength: 25,
                    responsive: true,
                });
            });
        </script>
        {{-- @include('potongan.js') --}}
    @endpush
