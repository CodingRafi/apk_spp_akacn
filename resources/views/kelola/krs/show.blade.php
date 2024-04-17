@extends('mylayouts.main')

@push('css')
    <style>
        .btn-full-width {
            width: 100%;
        }
    </style>
@endpush

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('verifikasi-krs.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Verifikasi KRS</h5>
                    </div>
                    <div class="d-flex">
                        @if ($data->status == 'pengajuan')
                            <form action="{{ route('verifikasi-krs.store', $data->id) }}" method="post"
                                class="form-verifikasi">
                                @csrf
                                <input type="hidden" name="status">
                                <input type="hidden" name="tgl_mulai">
                                <input type="hidden" name="tgl_akhir">
                                <button type="button" class="btn btn-primary" data-value="diterima">Diterima</button>
                                <button type="button" class="btn btn-danger" data-value="ditolak">Ditolak</button>
                            </form>
                        @else
                            <form action="{{ route('verifikasi-krs.revisi', $data->id) }}" method="post">
                                @method('patch')
                                @csrf
                                <button class="btn btn-revisi btn-warning" type="submit"
                                    onclick="return confirm('Apakah anda yakin ingin merevisi ini?')">Revisi</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($data->status == 'diterima')
                        <div class="alert alert-success">
                            KRS ini sudah diterima
                        </div>
                    @elseif($data->status == 'ditolak')
                        <div class="alert alert-danger">
                            KRS ini ditolak. Tanggal revisi : {{ parseDate($data->tgl_mulai_revisi) }} -
                            {{ parseDate($data->tgl_akhir_revisi) }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>Nama</th>
                                    <th>:</th>
                                    <th>{{ $data->name }}</th>
                                </tr>
                                <tr>
                                    <th>NIM</th>
                                    <th>:</th>
                                    <th>{{ $data->login_key }}</th>
                                </tr>
                                <tr>
                                    <th>Dosen PA</th>
                                    <th>:</th>
                                    <th>{{ $data->dosen_pa }}</th>
                                </tr>
                                <tr>
                                    <th>Prodi</th>
                                    <th>:</th>
                                    <th>{{ $data->prodi }}</th>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>Tahun Masuk</th>
                                    <th>:</th>
                                    <th>{{ $data->tahun_masuk }}</th>
                                </tr>
                                <tr>
                                    <th>Pengajuan Semester</th>
                                    <th>:</th>
                                    <th>{{ $data->semester }}</th>
                                </tr>
                                <tr>
                                    <th>Rombel</th>
                                    <th>:</th>
                                    <th>{{ $data->rombel }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <h5 class="text-capitalize">Mata Kuliah</h5>
                        @if ($data->status == 'pengajuan')
                            <button type="button" class="btn btn-primary"
                                onclick="addForm('{{ route('krs.store', ['tahun_semester_id' => $data->tahun_semester_id, 'mhs_id' => $data->mhs_id]) }}', 'Tambah Mata Kuliah', '#addMatkul', getMatkul)">
                                Tambah Mata Kuliah
                            </button>
                        @endif
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-matkul">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>SKS</th>
                                    <th>Dosen</th>
                                    <th>Ruang</th>
                                    @if ($data->status == 'pengajuan')
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
        'tahun_semester_id' => $data->tahun_semester_id,
        'mhs_id' => $data->mhs_id,
        'jatah_sks' => $data->jatah_sks,
        'check_tgl' => $data->status == 'pengajuan' ? true : false,
    ])
    <script>
        function isValidDate(dateString) {
            const regex = /^\d{4}-\d{2}-\d{2}$/;
            if (!regex.test(dateString)) {
                return false;
            }

            const date = new Date(dateString);
            return !isNaN(date.getTime());
        }

        $('.form-verifikasi button').on('click', function() {
            $('input[name=status]').val($(this).data('value'));
            if ($(this).data('value') == 'diterima') {
                $('.form-verifikasi').submit();
            } else {
                Swal.fire({
                    title: 'Apakah anda yakin akan menolak KRS ini?',
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
                            title: '',
                            html: `
                                <h2 class="my-4">Silahkan input Tanggal Revisi</h2>
                                <div class="mb-3">
                                    <label for="tgl_mulai_revisi" class="form-label">Tanggal Mulai Revisi</label>
                                    <input class="form-control" type="date" id="tgl_mulai_revisi" name="tgl_mulai_revisi" required />
                                </div>
                                <div class="mb-3">
                                    <label for="tgl_akhir_revisi" class="form-label">Tanggal Akhir Revisi</label>
                                    <input class="form-control" type="date" id="tgl_akhir_revisi" name="tgl_akhir_revisi" required />
                                </div>
                            `,
                            showCancelButton: false,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Cancel',
                            preConfirm: () => {
                                const tgl_mulai = document.getElementById('tgl_mulai_revisi')
                                    .value;
                                const tgl_akhir = document.getElementById('tgl_akhir_revisi')
                                    .value;

                                if (!tgl_mulai || !tgl_akhir) {
                                    Swal.showValidationMessage(
                                        'Harap lengkapi semua kolom!');
                                }

                                if (!isValidDate(tgl_mulai) || !isValidDate(tgl_akhir)) {
                                    Swal.showValidationMessage(
                                        'Tanggal tidak valid!');
                                }

                                return [tgl_mulai, tgl_akhir];
                            },
                            customClass: {
                                confirmButton: 'btn-full-width',
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const [tgl_mulai, tgl_akhir] = result.value;
                                $('.form-verifikasi input[name=tgl_mulai]').val(tgl_mulai);
                                $('.form-verifikasi input[name=tgl_akhir]').val(tgl_akhir);
                                $('.form-verifikasi').submit();
                            }
                        });
                    }
                })
            }
        })
    </script>
@endpush
