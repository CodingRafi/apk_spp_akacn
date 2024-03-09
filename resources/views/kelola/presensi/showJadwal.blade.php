@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('kelola-presensi.presensi.show', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Pertemuan</h5>
                    </div>
                    @if (getRole()->name != 'admin')
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            @if ($data->pengajar_id == Auth::user()->id)
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#jadwal">Edit
                                    Jadwal</button>
                                @if ($data->presensi_mulai && !$data->presensi_selesai)
                                    <form
                                        action="{{ route('kelola-presensi.presensi.selesaiJadwal', ['jadwal_id' => $data->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <button class="btn btn-danger" type="submit">Selesai Pelajaran</button>
                                    </form>
                                @elseif(!$data->presensi_mulai)
                                    <form
                                        action="{{ route('kelola-presensi.presensi.mulaiJadwal', ['jadwal_id' => $data->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('put')
                                        <button class="btn btn-primary" type="submit">Mulai Pelajaran</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <td>Kode Presensi</td>
                                    <td>{{ $data->kode }}</td>
                                </tr>
                                <tr>
                                    <td>Pengajar</td>
                                    <td>{{ $data->pengajar }}</td>
                                </tr>
                                <tr>
                                    <td>Presensi Masuk Pengajar</td>
                                    <td>{{ $data->presensi_mulai }}</td>
                                </tr>
                                <tr>
                                    <td>Presensi Pulang Pengajar</td>
                                    <td>{{ $data->presensi_selesai }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <td>Tanggal Pembelajaran</td>
                                    <td>{{ parseDate($data->tgl) }}</td>
                                </tr>
                                <tr>
                                    <td>Matkul</td>
                                    <td>{{ $data->matkul }}</td>
                                </tr>
                                <tr>
                                    <td>Materi</td>
                                    <td>{{ $data->materi }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="mt-3">
                                <label for="ket" class="form-label">Keterangan</label>
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
    <div class="modal fade" id="presensi" tabindex="-1" aria-labelledby="presensiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="presensiLabel">Edit Presensi</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    @if ($data->pengajar_id == Auth::user()->id)
        <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('kelola-presensi.presensi.updateJadwal', ['jadwal_id' => $data->id]) }}">
                        @method('put')
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="jadwalLabel">Edit Jadwal</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="materi" class="form-label">Materi</label>
                                <input class="form-control" type="text" name="materi"
                                    value="{{ $data->materi }}" />
                            </div>
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
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
            "{{ route('kelola-presensi.presensi.getPresensiMhs', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'jadwal_id' => request('jadwal_id'), 'rombel_id' => ':rombel_id', 'mhs_id' => ':mhs_id']) }}";

        function generate_table(data) {
            let table = '';

            data.forEach(e => {
                table +=
                    `<tr>
                        <td>${e.name}</td>
                        <td>${e.login_key}</td>
                        <td><button class="bg-transparen border-none" onclick="editForm('${url_edit_presensi.replace(':mhs_id', e.id).replace(':rombel_id', e.rombel_id)}', 'Edit Presensi', '#presensi')">${e.status ?? '-'}</button></td>
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
                    url: "{{ route('kelola-presensi.presensi.getPresensi', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'jadwal_id' => request('jadwal_id'), 'rombel_id' => ':rombel_id']) }}"
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
