@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('data-master.tahun-ajaran.matkul.index', ['id' => request('id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="mb-0">Dosen Mata Kuliah</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('data-master.tahun-ajaran.matkul.dosen.store', ['id' => request('id'), 'matkul_id' => request('matkul_id')]) }}', 'Tambah Dosen', '#setDosen', addDosen)">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>SKS</th>
                                    <th>Jumlah Rencana Pertemuan</th>
                                    <th>Jumlah Realisasi Pertemuan</th>
                                    <th>Jenis Evaluasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="setDosen" tabindex="-1" role="dialog" aria-labelledby="setDosenLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-set-dosen">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="setDosenLabel">Tambah Dosen</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen</label>
                            <select class="form-select" name="dosen_id" id="dosen_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->name }} ({{ $dosen->login_key }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sks_substansi_total" class="form-label">SKS</label>
                            <input type="text" class="form-control" name="sks_substansi_total">
                        </div>
                        <div class="mb-3">
                            <label for="rencana_tatap_muka" class="form-label">Jumlah Rencana Pertemuan</label>
                            <input type="text" class="form-control" name="rencana_tatap_muka">
                        </div>
                        <div class="mb-3">
                            <label for="realisasi_tatap_muka" class="form-label">Jumlah Realisasi Pertemuan</label>
                            <input type="text" class="form-control" name="realisasi_tatap_muka">
                        </div>
                        <div class="mb-3">
                            <label for="jenis_evaluasi_id" class="form-label">Jenis Evaluasi</label>
                            <select class="form-select" name="jenis_evaluasi_id" id="jenis_evaluasi_id">
                                <option value="">Pilih Jenis Evaluasi</option>
                                @foreach ($jenisEvaluasi as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableDosen.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let tableDosen;

        $(document).ready(function() {
            tableDosen = $('.table-matkul').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.tahun-ajaran.matkul.dosen.data', ['id' => request('id'), 'matkul_id' => request('matkul_id')]) }}'
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "sks_substansi_total"
                    },
                    {
                        "data": "rencana_tatap_muka"
                    },
                    {
                        "data": "realisasi_tatap_muka"
                    },
                    {
                        "data": "jenisEvaluasi"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });

        $('#filter-prodi').on('change', function() {
            tableDosen.ajax.reload();
        });

        function editDosen() {
            $('#dosen_id').attr('disabled', 'disabled');
        }

        function addDosen() {
            $('#dosen_id').removeAttr('disabled');
        }
    </script>
@endpush
