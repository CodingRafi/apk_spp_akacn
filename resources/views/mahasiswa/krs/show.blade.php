    @extends('mylayouts.main')

    @section('container')
        @php
            $validation = true;
            $dataEmpty = true;
            $lock = true;

            if ($krs) {
                $lock = $krs->lock == '1' ? false : true;
                $dataEmpty = false;
                if ($krs->status == 'pending') {
                    $validation =
                        $tahun_semester->tgl_mulai_krs <= date('Y-m-d') &&
                        $tahun_semester->tgl_akhir_krs >= date('Y-m-d') &&
                        $tahun_semester->status &&
                        ($krs->lock == '1' && !Auth::user()->hasRole('admin') ? false : true);
                } elseif ($krs->status == 'ditolak') {
                    $validation =
                        (Auth::user()->hasRole('admin')
                            ? true
                            : $tahun_semester->tgl_mulai_krs <= date('Y-m-d') &&
                                $tahun_semester->tgl_akhir_krs >= date('Y-m-d')) && $tahun_semester->status;
                } else {
                    $validation = false;
                }
            } else {
                $validation =
                    $tahun_semester->status &&
                    $tahun_semester->tgl_mulai_krs <= date('Y-m-d') &&
                    $tahun_semester->tgl_akhir_krs >= date('Y-m-d') &&
                    (Auth::user()->hasRole('admin') ? true : $validationPembayaran['status']);
            }
        @endphp
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if (Auth::user()->hasRole('mahasiswa'))
                                <a href="{{ route('krs.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @else
                                <a href="{{ route('kelola-users.mahasiswa.show', $mhs_id) }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @endif
                            <h5 class="text-capitalize mb-0">KRS {{ $tahun_semester->nama }}</h5>
                        </div>
                        <div class="d-flex" style="gap: 1rem;">
                            @if ($validation)
                                @if ($dataEmpty || ($krs->status == 'pending' || $krs->status == 'ditolak'))
                                    <button type="button" class="btn btn-primary"
                                        onclick="addForm('{{ route('krs.store', ['tahun_semester_id' => $tahun_semester->id, 'mhs_id' => $mhs_id]) }}', 'Tambah Mata Kuliah', '#addMatkul', getMatkul)">
                                        Tambah Mata Kuliah
                                    </button>
                                    @if ($dataEmpty || $krs->status == 'pending')
                                        @if (Auth::user()->hasRole('mahasiswa'))
                                            <form action="{{ route('krs.ajukan', ['tahun_semester_id' => $tahun_semester->id, 'mhs_id' => $mhs_id]) }}"
                                                class="form-ajukan" method="POST">
                                                @csrf
                                                <button type="button" class="btn btn-warning btn-ajukan">Ajukan
                                                    KRS</button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('krs.simpan', ['tahun_semester_id' => $tahun_semester->id, 'mhs_id' => $mhs_id]) }}"
                                                class="form-ajukan" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">Simpan</button>
                                            </form>
                                        @endif
                                    @elseif($krs->status == 'ditolak')
                                        <form action="{{ route('krs.revisi', $tahun_semester->id) }}" method="post"
                                            class="form-revisi">
                                            @csrf
                                            @method('patch')
                                            <button type="button" class="btn btn-warning btn-revisi">Kirim Revisi</button>
                                        </form>
                                    @endif
                                @endif
                            @endif
                            @if (
                                (Auth::user()->hasRole('admin') && (($krs && $krs->status == 'pending' && $krs->lock == '0') ||
                                    (!$krs && !$validation))))
                                <form
                                    action="{{ route('krs.updateLock', ['mhs_id' => $mhs_id, 'tahun_semester_id' => $tahun_semester->id]) }}"
                                    method="post">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="lock" value="1" aria-hidden="true">
                                    <button type="submit" class="btn btn-danger">lock</button>
                                </form>
                            @endif
                            @if (
                                (Auth::user()->hasRole('admin') && (($krs && $krs->status == 'pending' && $krs->lock == '1') ||
                                    (!$krs && $validation))))
                                <form
                                    action="{{ route('krs.updateLock', ['mhs_id' => $mhs_id, 'tahun_semester_id' => $tahun_semester->id]) }}"
                                    method="post">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="lock" value="0" aria-hidden="true">
                                    <button type="submit" class="btn btn-danger">unlock</button>
                                </form>
                            @endif

                            @if (Auth::user()->hasRole('mahasiswa') && $krs && $krs->status == 'diterima')
                                <a href="{{ route('krs.print', request('tahun_semester_id')) }}"
                                    class="btn btn-primary">Download KRS</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($dataEmpty || $krs->status == 'pending')
                            @if (!$validation)
                                <div class="alert alert-danger">
                                    Tidak dapat mengisi KRS
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Silahkan pilih mata kuliah yang ingin diambil. Maximal
                                    <strong>{{ $tahun_semester->jatah_sks }}</strong> SKS. Tanggal pengisian
                                    <strong>{{ parseDate($tahun_semester->tgl_mulai_krs) }}</strong> -
                                    <strong>{{ parseDate($tahun_semester->tgl_akhir_krs) }}</strong>
                                </div>
                            @endif
                        @elseif($krs && $krs->status == 'diterima')
                            <div class="alert alert-success">
                                KRS ini sudah diterima
                            </div>
                        @elseif($krs && $krs->status == 'ditolak')
                            <div class="alert alert-danger">
                                KRS ini ditolak. Tanggal revisi : {{ parseDate($krs->tgl_mulai_revisi) }} -
                                {{ parseDate($krs->tgl_akhir_revisi) }}
                            </div>
                        @endif

                        @if ($krs && $krs->status == 'pengajuan')
                            <div class="alert alert-info">
                                KRS ini sedang diajukan
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-matkul w-100">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>SKS</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Dosen</th>
                                        <th>Ruang</th>
                                        @if ($validation)
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

    @endsection

    @push('js')
        @include('krs.js', [
            'tahun_semester_id' => $tahun_semester->id,
            'jatah_sks' => $tahun_semester->jatah_sks,
            'mhs_id' => $mhs_id,
            'check_tgl' => $validation,
        ])
        <script>
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

            $('.btn-revisi').on('click', function() {
                Swal.fire({
                    title: "Apakah anda yakin ingin merevisi KRS?",
                    text: "Klik 'Ya' jika anda yakin",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.form-revisi').submit()
                    }
                });
            })
        </script>
    @endpush
