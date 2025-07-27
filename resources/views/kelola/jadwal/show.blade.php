@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.jadwal.tahun_matkul.indexTahunMatkul', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">{{ $data->type }}</h5>
                    </div>
                    @if (getRole()->name != 'admin')
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            @if ($data->pengajar_id == Auth::user()->id)
                                @if ($data->type == 'pertemuan')
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#jadwal">Edit
                                        Materi</button>
                                @endif
                                @if ($data->presensi_mulai && !$data->presensi_selesai)
                                    <form
                                        action="{{ route('kelola-presensi.jadwal.selesaiJadwal', ['jadwal_id' => $data->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <button class="btn btn-danger" type="submit">Selesai</button>
                                    </form>
                                @elseif(!$data->presensi_mulai)
                                    <form
                                        action="{{ route('kelola-presensi.jadwal.mulaiJadwal', ['jadwal_id' => $data->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <button class="btn btn-primary" type="submit">Mulai</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @else
                        @can('jadwal_approval')
                            @if ($data->approved != null)
                                @if ($data->approved == '1')
                                    <form action="{{ route('kelola-presensi.jadwal.storeApproval', ['jadwal_id' => $data->id]) }}"
                                        method="post" id="form-approval">
                                        @csrf
                                        <input type="hidden" name="status">
                                        <input type="hidden" name="ket_approved">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success" data-value="2"
                                                onclick="submitFormApproval(this)">Setujui</button>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#ModalApproval">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>
                                    <div class="modal fade" id="ModalApproval" tabindex="-1" aria-labelledby="ModalApprovalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header pb-0">
                                                    <h1 class="modal-title fs-5" id="ModalApprovalLabel">Jadwal Ditolak</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="ket_approval" class="form-label">Keterangan</label>
                                                    <textarea class="form-control" id="ket_approval" rows="3" name="ket_approval"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                    <button type="button" class="btn btn-primary" onclick="submitFormApproval(this)"
                                                        data-value="3">Simpan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        function submitFormApproval(el) {
                                            const status = el.getAttribute('data-value');
                                            const ket_approved = document.getElementById('ket_approval').value;
                                            document.querySelector('input[name=status]').value = status;
                                            document.querySelector('input[name=ket_approved]').value = ket_approved;
                                            document.querySelector('#form-approval').submit();
                                        }
                                    </script>
                                @else
                                    <form action="{{ route('kelola-presensi.jadwal.revisiApproval', ['jadwal_id' => $data->id]) }}" method="post">
                                        @csrf
                                        <button class="btn btn-warning" type="submit" onclick="return confirm('Apakah anda yakin ingin merevisi ini?')">Revisi</button>
                                    </form>
                                @endif
                            @endif
                        @endcan
                    @endif
                </div>
                @php
                    $typeUser = $data->type == 'pertemuan' ? 'Pengajar' : 'Pengawas';
                @endphp
                <div class="card-body">
                    @if ($data->approved != null)
                        @if ($data->approved == '1')
                            <div class="alert alert-warning">
                                Jadwal ini sedang menunggu verifikasi
                            </div>
                        @elseif($data->approved == '2')
                            <div class="alert alert-success">
                                Jadwal ini telah disetujui
                            </div>
                        @else
                            <div class="alert alert-danger">
                                Jadwal ini telah ditolak
                                <br>
                                Keterangan : {{ $data->ket_approved }}
                            </div>
                        @endif
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table" aria-hidden="true">
                                <tr>
                                    <td class="col-6">Kode Presensi</td>
                                    <td class="col-6">{{ $data->kode }}</td>
                                </tr>
                                <tr>
                                    <td class="col-6">{{ $typeUser }}</td>
                                    <td class="col-6">{{ $data->pengajar }}</td>
                                </tr>
                                <tr>
                                    <td class="col-6">Presensi Masuk {{ $typeUser }}</td>
                                    <td class="col-6">{{ $data->presensi_mulai }}</td>
                                </tr>
                                <tr>
                                    <td class="col-6">Presensi Pulang {{ $typeUser }}</td>
                                    <td class="col-6">{{ $data->presensi_selesai }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <td class="col-6">Tanggal</td>
                                    <td class="col-6">{{ parseDate($data->tgl) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-6">Matkul</td>
                                    <td class="col-6">{{ $data->matkul }}</td>
                                </tr>
                                @if ($data->type == 'pertemuan')
                                    <tr>
                                        <td class="col-6">Materi</td>
                                        <td class="col-6">{{ $data->materi }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="mt-3">
                                <label for="ket" class="form-label">Detail Materi</label>
                                <textarea class="form-control" disabled>{{ $data->ket }}</textarea>
                            </div>
                        </div>
                        <hr class="mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                Harap pilih rombel untuk melihat presensi
                            </div>
                            <select id="rombel_id" class="form-control" onchange="get_presensi()">
                                <option value="">Pilih Rombel</option>
                                @foreach ($rombel as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <div class="table-responsive mt-3">
                                <table class="table table-presensi">
                                    <thead>
                                        <tr>
                                            <td>Nama</td>
                                            <td>Nim</td>
                                            <td>Status</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (!$data->presensi_selesai || Auth::user()->hasRole('admin'))
        <div class="modal fade" id="presensi" tabindex="-1" aria-labelledby="presensiLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="">
                        @method('post')
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="presensiLabel">Edit Presensi</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input class="form-control" type="text" id="name" name="name" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="login_key" class="form-label">NIM</label>
                                <input class="form-control" type="text" id="login_key" name="login_key" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Pilih Status</option>
                                    @foreach (config('services.statusPresensi') as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                onclick="submitForm(this.form, this, () => get_presensi())">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @if ($data->pengajar_id == Auth::user()->id)
        <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('kelola-presensi.jadwal.updateJadwalMengajar', ['jadwal_id' => $data->id]) }}">
                        @method('put')
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="jadwalLabel">Edit Materi</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="materi_id" class="form-label">Materi</label>
                                <select name="materi_id" id="materi_id" class="form-control"
                                    {{ $data->presensi_selesai ? 'readonly' : '' }} disabled>
                                    <option value="">Pilih Materi</option>
                                    @foreach ($materi as $row)
                                        <option value="{{ $row->id }}"
                                            {{ $data->materi_id == $row->id ? 'selected' : '' }}>{{ $row->materi }}
                                            ({{ $row->type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ket" class="form-label">Detail Materi</label>
                                <textarea cols="30" rows="10" class="form-control" name="ket">{{ $data->ket }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                onclick="submitForm(this.form, this, () => {location.reload()})">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script>
        const url_edit_presensi =
            "{{ route('kelola-presensi.jadwal.tahun_matkul.getPresensiMhs', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => request('jadwal_id'), 'rombel_id' => ':rombel_id', 'mhs_id' => ':mhs_id']) }}";

        function generate_table(data) {
            let table = '';

            data.forEach(e => {
                table +=
                    `<tr>
                        <td>${e.name}</td>
                        <td>${e.login_key}</td>
                        @if ($data->presensi_selesai && Auth::user()->hasRole('dosen'))
                        <td>${e.status ?? '-'}</td>
                        @else
                        <td><button class="bg-transparen border-none" onclick="editForm('${url_edit_presensi.replace(':mhs_id', e.id).replace(':rombel_id', e.rombel_id)}', 'Edit Presensi', '#presensi')">${e.status ?? ''}</button></td>
                        @endif
                    </tr>`;
            });

            $('.table-presensi tbody').html(table);
        }

        function get_presensi() {
            $('.table-presensi tbody').empty();
            if ($('#rombel_id').val() != '') {
                $('.table-presensi tbody').append(`<tr>
                                                        <td colspan="3" class="text-center py-4">
                                                            <div class="spinner-border" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                    </tr>`);
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.tahun_matkul.getPresensi', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => request('jadwal_id'), 'rombel_id' => ':rombel_id']) }}"
                        .replace(':rombel_id', $('#rombel_id').val()),
                    type: 'GET',
                    dataType: "json",
                    success: function(res) {
                        generate_table(res.data)
                    },
                    error: function() {
                        alert('Gagal get presensi')
                    }
                })
            }
        }
    </script>
@endpush
