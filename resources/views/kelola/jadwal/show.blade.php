@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('kelola-presensi.jadwal.tahun_matkul.indexTahunMatkul', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">{{ $data->type }}</h5>
                    </div>
                    @if (getRole()->name != 'admin')
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            @if ($data->pengajar_id == Auth::user()->id and !is_null($data->approved))
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#jadwal">Edit Detail
                                    @if ($data->type == 'pertemuan')
                                        Materi
                                    @else
                                        Situasi
                                    @endif
                                </button>
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
                                @if ($data->type == 'ujian' && $data->presensi_selesai)
                                    <a href="{{ route('kelola-presensi.jadwal.tahun_matkul.berita-acara', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => $data->id]) }}"
                                        class="btn btn-primary">Berita Acara</a>
                                @endif
                            @endif
                        </div>
                    @else
                        @can('jadwal_approval')
                            @if ($data->presensi_mulai)
                                @if ($data->approved == '1')
                                    <form
                                        action="{{ route('kelola-presensi.jadwal.storeApproval', ['jadwal_id' => $data->id]) }}"
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
                                    <div class="modal fade" id="ModalApproval" tabindex="-1"
                                        aria-labelledby="ModalApprovalLabel" aria-hidden="true">
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
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="submitFormApproval(this)" data-value="3">Simpan</button>
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
                                    <form
                                        action="{{ route('kelola-presensi.jadwal.revisiApproval', ['jadwal_id' => $data->id]) }}"
                                        method="post">
                                        @csrf
                                        <button class="btn btn-warning" type="submit"
                                            onclick="return confirm('Apakah anda yakin ingin merevisi ini?')">Revisi</button>
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
                                <tr>
                                    <td class="col-6">Tanggal</td>
                                    <td class="col-6">{{ parseDate($data->tgl) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table" aria-hidden="true">
                                <tr>
                                    <td class="col-6">Matkul</td>
                                    <td class="col-6">{{ $data->matkul }}</td>
                                </tr>
                                @if ($data->type == 'pertemuan')
                                    <tr>
                                        <td class="col-6">Materi</td>
                                        <td class="col-6">{{ $data->materi }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="col-6">Tingkat</td>
                                        <td class="col-6">{{ $data->tingkat }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-6">Ruang</td>
                                        <td class="col-6">{{ $data->ruang }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-6">Sifat Ujian</td>
                                        <td class="col-6">{{ $data->sifat_ujian }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="mt-3">
                                <label for="ket" class="form-label">Detail
                                    @if ($data->type == 'pertemuan')
                                        Materi
                                    @else
                                        Situasi
                                    @endif
                                </label>
                                <textarea class="form-control" disabled>{{ $data->ket }}</textarea>
                            </div>
                        </div>
                        @if ($data->type == 'ujian')
                            <div class="col-md-12">
                                <div class="mt-3">
                                    <label for="ket" class="form-label">Status Ujian
                                    </label>
                                    <textarea class="form-control" disabled>{{ $data->status_ujian }}</textarea>
                                </div>
                            </div>
                        @endif
                        <hr class="mt-3">
                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Cari berdasarkan Nama, NIM, atau Rombel...">
                                @if (($data->presensi_mulai && !$data->presensi_selesai) || Auth::user()->hasRole('admin'))
                                    <select id="status_presensi" class="form-control" style="width: 10rem;">
                                        <option value="">Pilih Status</option>
                                        @foreach (config('services.statusPresensi') as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary"
                                        onclick="update_presensi_many_mhs()">Simpan</button>
                                @endif
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-presensi" aria-hidden="true">
                                    <thead>
                                        <tr>
                                            @if (($data->presensi_mulai && !$data->presensi_selesai) || Auth::user()->hasRole('admin'))
                                                <td style="width: 15px;"><input type="checkbox" onchange="checkAll(this)"
                                                        id="checkAll"></td>
                                            @endif
                                            <td>Nama</td>
                                            <td>Nim</td>
                                            <td>Rombel</td>
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
                            <h1 class="modal-title fs-5" id="jadwalLabel">Edit @if ($data->type == 'pertemuan')
                                    Materi
                                @else
                                    Situasi
                                @endif
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if ($data->type == 'pertemuan')
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
                            @endif
                            <div class="mb-3">
                                <label for="ket" class="form-label">Detail @if ($data->type == 'pertemuan')
                                        Materi
                                    @else
                                        Situasi
                                    @endif
                                </label>
                                <textarea cols="30" rows="10" class="form-control" name="ket">{{ $data->ket }}</textarea>
                            </div>
                            @if ($data->type == 'ujian')
                                <div class="mb-3">
                                    <label for="status_ujian" class="form-label">Status Ujian</label>
                                    <textarea cols="30" rows="10" class="form-control" name="status_ujian">{{ $data->status_ujian }}</textarea>
                                </div>
                            @endif
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
            "{{ route('kelola-presensi.jadwal.tahun_matkul.getPresensiMhs', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => request('jadwal_id'), 'mhs_id' => ':mhs_id']) }}";

        function syncCheckAll() {
            const allCheckboxes = $('input[name="mhs_id[]"]');
            const checkedCheckboxes = $('input[name="mhs_id[]"]:checked');
            $('#checkAll').prop('checked', allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length);
        }

        // Fungsi untuk pasang event listener pada checkbox mahasiswa
        function attachCheckboxListeners() {
            $(document).off('change', 'input[name="mhs_id[]"]').on('change', 'input[name="mhs_id[]"]', function() {
                syncCheckAll();
            });
        }

        // Fungsi checkAll yang sudah diperbaiki
        function checkAll(e) {
            const isChecked = e.checked;
            $('input[name="mhs_id[]"]').prop('checked', isChecked);
        }

        function generate_table(data) {
            let table = '';

            data.forEach(e => {
                table +=
                    `<tr data-login-key="${e.login_key}" data-name="${e.name}" data-rombel="${e.rombel}">
                        @if (($data->presensi_mulai && !$data->presensi_selesai) || Auth::user()->hasRole('admin'))
                        <td style="width: 15px;"><input type="checkbox" name="mhs_id[]" id="mhs_id" value="${e.id}" class="mhs-checkbox"></td>
                        @endif
                        <td>${e.name}</td>
                        <td>${e.login_key}</td>
                        <td>${e.rombel}</td>
                        @if (($data->presensi_mulai && !$data->presensi_selesai) || Auth::user()->hasRole('admin'))
                        <td><button class="bg-transparen border-none" onclick="editForm('${url_edit_presensi.replace(':mhs_id', e.id)}', 'Edit Presensi', '#presensi')">${e.status ?? ''}</button></td>
                        @else
                        <td>${e.status ?? '-'}</td>
                        @endif
                    </tr>`;
            });

            $('.table-presensi tbody').html(table);

            syncCheckAll();
            attachCheckboxListeners();
        }

        function get_presensi() {
            $('.table-presensi tbody').empty();
            $('.table-presensi tbody').append(`<tr>
                                                    @if (($data->presensi_mulai && !$data->presensi_selesai) || Auth::user()->hasRole('admin'))
                                                    <td colspan="5" class="text-center py-4">
                                                    @else
                                                    <td colspan="4" class="text-center py-4">
                                                    @endif
                                                        <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </td>
                                                </tr>`);
            $.ajax({
                url: "{{ route('kelola-presensi.jadwal.tahun_matkul.getPresensi', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => request('jadwal_id')]) }}",
                type: 'GET',
                dataType: "json",
                success: function(res) {
                    $('#checkAll').prop('checked', false);
                    generate_table(res.data)
                    $('#searchInput').trigger('keyup');
                },
                error: function() {
                    alert('Gagal get presensi')
                }
            })
        }

        function update_presensi_many_mhs() {
            const mhs_ids = $('input[name="mhs_id[]"]:checked')
                .map(function() {
                    return $(this).val();
                }).get();

            const status = $('#status_presensi').val();

            if (mhs_ids.length == 0) {
                showAlert('Tidak ada mahasiswa yang dipilih', 'error');
                return;
            }

            if (status == '') {
                showAlert('Status presensi tidak boleh kosong', 'error');
                return;
            }

            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.tahun_matkul.updatePresensiManyMhs', ['tahun_matkul_id' => request('tahun_matkul_id'), 'jadwal_id' => request('jadwal_id')]) }}',
                method: 'POST',
                data: {
                    mhs_ids: mhs_ids,
                    status: status
                },
                success: function(response) {
                    showAlert(response.message, 'success');
                    get_presensi();
                    $('#status_presensi').val('');
                },
                error: function(err) {
                    console.error('Gagal:', err);
                }
            });
        }

        $(document).ready(function() {
            get_presensi();

            $('#searchInput').on('keyup', function() {
                const filter = $(this).val().toLowerCase();
                $('.table-presensi tbody tr').each(function() {
                    const name = this.getAttribute('data-name').toLowerCase() || '';
                    const loginKey = this.getAttribute('data-login-key')?.toLowerCase() || '';
                    const rombel = this.getAttribute('data-rombel')?.toLowerCase() || '';

                    if (name.includes(filter) || loginKey.includes(filter) || rombel.includes(
                            filter)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endpush
