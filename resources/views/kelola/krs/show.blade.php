@extends('mylayouts.main')

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
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-6">
                        <table class="table table-bordered">
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
                                <th>Prodi</th>
                                <th>:</th>
                                <th>{{ $data->prodi }}</th>
                            </tr>
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
                        </table>
                    </div>
                    <div class="table-responsive mt-3">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-capitalize">Mata Kuliah</h5>
                            <button type="button" class="btn btn-primary"
                                onclick="addForm('{{ route('krs.store', $data->tahun_semester_id) }}', 'Tambah Mata Kuliah', '#addMatkul', getMatkul)">
                                Tambah Mata Kuliah
                            </button>
                        </div>
                        <table class="table mt-3 table-matkul">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>SKS</th>
                                    <th>Dosen</th>
                                    <th>Ruang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('krs.js', [
        'tahun_semester_id' => $data->tahun_semester_id,
        'jatah_sks' => $data->jatah_sks,
    ])
@endsection

@push('js')
    <script>
        let tableMatkul;
        $(document).ready(function() {
            tableMatkul = $('.table-matkul').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('verifikasi-krs.dataMatkul', ['id' => request('id')]) }}',
                columns: [{
                        "data": "kode"
                    },
                    {
                        "data": "matkul"
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
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
