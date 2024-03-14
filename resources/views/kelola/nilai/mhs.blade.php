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
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" aria-label="Data rombel">
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
                <form action="" method="get">
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
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
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
            table = $('.table').DataTable({
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
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
