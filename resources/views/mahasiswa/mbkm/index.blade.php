@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">MBKM</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-mbkm" aria-label="table-mbkm">
                            <thead>
                                <tr>
                                    <th class="col-4">No</th>
                                    <th class="col-4">Judul</th>
                                    <th class="col-4">Aksi</th>
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
    <div class="modal fade" id="Mbkm" tabindex="-1" aria-labelledby="MbkmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="MbkmLabel">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_anggota" class="form-label">Jenis Anggota</label>
                            <select class="form-select" name="jenis_anggota" id="jenis_anggota" disabled>
                                <option value="">Pilih Jenis Anggota</option>
                                <option value="0">Personal</option>
                                <option value="1">Kelompok</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_aktivitas_id" class="form-label">Jenis Aktivitas</label>
                            <select class="form-select" name="jenis_aktivitas_id" id="jenis_aktivitas_id" disabled>
                                <option value="">Pilih Jenis Aktivitas</option>
                                @foreach ($jenisAktivitas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_semester_id" class="form-label">Semester</label>
                            <select class="form-select" name="tahun_semester_id" id="tahun_semester_id" disabled>
                                <option value="">Pilih Semester</option>
                                @foreach ($semester as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="ket" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="textarea-mbkm" rows="3" name="ket" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi" class="form-label">lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="sk_tugas" class="form-label">SK Tugas</label>
                            <input type="text" class="form-control" id="sk_tugas" name="sk_tugas" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_sk_tugas" class="form-label">Tanggal SK Tugas</label>
                            <input type="date" class="form-control" id="tgl_sk_tugas" name="tgl_sk_tugas" disabled>
                        </div>
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
                ajax: '{{ route('mbkm.data') }}',
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "judul"
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
