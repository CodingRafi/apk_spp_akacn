    @extends('mylayouts.main')

    @section('container')
        @php
            $check_tgl = $tahun_semester->tgl_mulai_krs <= date('Y-m-d') && $tahun_semester->tgl_akhir_krs >= date('Y-m-d') && ($krs ? $krs->status == 'pending' : true);
        @endphp
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('krs.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">KRS {{ $tahun_semester->nama }}</h5>
                        </div>
                        @if ($check_tgl)
                            <div class="d-flex" style="gap: 1rem;">
                                <button type="button" class="btn btn-primary"
                                    onclick="addForm('{{ route('krs.store', $tahun_semester->id) }}', 'Tambah Mata Kuliah', '#addMatkul', getMatkul)">
                                    Tambah Mata Kuliah
                                </button>
                                <form action="{{ route('krs.ajukan', $tahun_semester->id) }}" class="form-ajukan"
                                    method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-warning btn-ajukan">Ajukan KRS</button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (!$krs || ($krs && $krs->status == 'pending'))
                            @if (!$check_tgl)
                                <div class="alert alert-danger">
                                    Bukan waktu pengisian KRS
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Silahkan pilih mata kuliah yang ingin diambil. Maximal
                                    <strong>{{ $tahun_semester->jatah_sks }}</strong> SKS. Tanggal pengisian
                                    <strong>{{ parseDate($tahun_semester->tgl_mulai_krs) }}</strong> -
                                    <strong>{{ parseDate($tahun_semester->tgl_akhir_krs) }}</strong>
                                </div>
                            @endif
                        @endif

                        @if ($krs && $krs->status == 'pengajuan')
                            <div class="alert alert-info">
                                KRS ini sedang diajukan
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-pembayaran">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>SKS</th>
                                        <th>Dosen</th>
                                        <th>Ruang</th>
                                        @if ($check_tgl)
                                            <th>Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($check_tgl)
            @include('krs.js', [
                'tahun_semester_id' => $tahun_semester->id,
                'jatah_sks' => $tahun_semester->jatah_sks
            ])
        @endif
    @endsection

    @push('js')
        <script>
            let table;

            $(document).ready(function() {
                table = $('.table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('krs.dataMatkul', ['tahun_semester_id' => $tahun_semester->id]) }}',
                    columns: [{
                            "data": "DT_RowIndex"
                        },
                        {
                            "data": "kode"
                        },
                        {
                            "data": "nama"
                        },
                        {
                            "data": "sks_mata_kuliah"
                        },
                        {
                            "data": "dosen"
                        },
                        {
                            "data": "ruang"
                        },
                        @if ($check_tgl)
                            {
                                "data": "options"
                            }
                        @endif
                    ],
                    pageLength: 25,
                    responsive: true,
                });
            });

            $('.btn-ajukan').on('click', function() {
                Swal.fire({
                    title: "Apakah anda yakin ingin mengajukan KRS?",
                    text: "Klik 'Ya' jika anda yakin",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.form-ajukan').submit()
                    }
                });
            })
        </script>
    @endpush
