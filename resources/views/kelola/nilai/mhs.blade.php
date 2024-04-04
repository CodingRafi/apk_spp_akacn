@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-nilai.show', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Nilai</h5>
                    </div>
                    <div class="d-flex" style="gap: 1rem;">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#petunjuk"
                            aria-expanded="false" aria-controls="petunjuk">
                            Petunjuk Import Nilai
                        </button>
                        <a href="{{ route('kelola-nilai.downloadTemplate', [
                            'tahun_ajaran_id' => request('tahun_ajaran_id'),
                            'rombel_id' => request('rombel_id'),
                            'tahun_matkul_id' => request('tahun_matkul_id'),
                            'tahun_semester_id' => request('tahun_semester_id'),
                        ]) }}"
                            class="btn btn-primary">Download Template</a>
                        <button class="btn btn-primary" type="button"
                            onclick="addForm('{{ route('kelola-nilai.importNilai', [
                                'tahun_ajaran_id' => request('tahun_ajaran_id'),
                                'rombel_id' => request('rombel_id'),
                                'tahun_matkul_id' => request('tahun_matkul_id'),
                                'tahun_semester_id' => request('tahun_semester_id'),
                            ]) }}', 'Import Nilai', '#importNilai')">
                            Import Nilai
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="collapse mb-3" id="petunjuk">
                        <div class="card card-body">
                            <ol>
                                <li>Klik tombol "Download Template" untuk mengunduh template yang diperlukan.</li>
                                <li>
                                    Untuk mutu menggunakan id mutu seperti dibawah ini
                                    <br>
                                    <div class="w-50">
                                        <table class="table" aria-label="table-mutu">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Nama</th>
                                                    <th>Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($mutu as $row)
                                                    <tr>
                                                        <td>{{ $row->id }}</td>
                                                        <td>{{ $row->nama }}</td>
                                                        <td>{{ $row->nilai }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </li>
                                <li>
                                    Publish diisi dengan angka 1 jika ingin menampilkan data, dan 0 jika ingin
                                    menyembunyikan.
                                </li>
                                <li>
                                    Klik tombol "Import Nilai", dan pilih file template yang telah diisi.
                                    <br>
                                    <small class="text-danger">Catatan: Seluruh data yang ada akan direplace dengan data
                                        yang diimport.</small>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-mhs" aria-label="Data rombel">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Presensi</th>
                                    <th>UTS</th>
                                    <th>UAS</th>
                                    <th>Mutu</th>
                                    <th>Nilai Mutu</th>
                                    <th>Jumlah SKS</th>
                                    <th>Publish</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="nilai" tabindex="-1" aria-labelledby="nilaiLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="nilaiLabel">Input Nilai</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="name" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input class="form-control" type="text" id="nim" name="login_key" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="presensi" class="form-label">presensi</label>
                            <input class="form-control" type="number" id="presensi" name="presensi" min="0"
                                step="0.01" />
                        </div>
                        <div class="mb-3">
                            <label for="uts" class="form-label">UTS</label>
                            <input class="form-control" type="number" id="uts" name="uts" min="0"
                                step="0.01" />
                        </div>
                        <div class="mb-3">
                            <label for="uas" class="form-label">UAS</label>
                            <input class="form-control" type="number" id="uas" name="uas" min="0"
                                step="0.01" />
                        </div>
                        <div class="mb-3">
                            <label for="mutu_id" class="form-label">Mutu</label>
                            <select name="mutu_id" id="mutu_id" class="form-control">
                                <option value="">Pilih Mutu</option>
                                @foreach ($mutu as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="publish" class="form-label">Publish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="publish"
                                    value="1" id="publish">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importNilai" tabindex="-1" aria-labelledby="importNilaiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="importNilaiLabel">Import Nilai</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">File</label>
                            <input class="form-control" type="file" id="file" name="file" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this)">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table-mhs').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-nilai.dataMhs', ['tahun_ajaran_id' => request('tahun_ajaran_id'), 'rombel_id' => request('rombel_id'), 'tahun_semester_id' => request('tahun_semester_id'), 'tahun_matkul_id' => request('tahun_matkul_id')]) }}'
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "login_key"
                    },
                    {
                        "data": "presensi"
                    },
                    {
                        "data": "uts"
                    },
                    {
                        "data": "uas"
                    },
                    {
                        "data": "mutu"
                    },
                    {
                        "data": "nilai_mutu"
                    },
                    {
                        "data": "jml_sks"
                    },
                    {
                        "data": "publish"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
