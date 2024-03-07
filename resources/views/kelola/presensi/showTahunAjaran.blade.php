@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Presensi {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('data-master.ruang.store') }}', 'Tambah Jadwal', '#jadwal')">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Presensi</th>
                                    <th>Kode Matkul</th>
                                    <th>Tanggal</th>
                                    <th>Matkul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('kelola-presensi.whitelist-ip.store') }}" method="get">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="jadwalLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Pilih Type</option>
                                <option value="ujian">Ujian</option>
                                <option value="pertemuan">Pertemuan</option>
                            </select>
                        </div>
                        <div class="div-ujian"></div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Presensi</label>
                            <div class="d-flex" style="gap: 1rem;">
                                <input class="form-control" type="text" id="kode" name="kode" />
                                <button class="btn btn-primary btn-generate" onclick="generateCode()"
                                    type="button">Generate</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                            <select name="tahun_matkul_id" id="tahun_matkul_id" class="form-select" onchange="getTotal()">
                                <option value="">Pilih Pelajaran</option>
                                @foreach ($tahunMatkul as $matkul)
                                    <option value="{{ $matkul->id }}">{{ $matkul->nama }} | {{ $matkul->rombel }}
                                        |
                                        {{ config('services.hari')[$matkul->hari] }}, {{ $matkul->jam_mulai }} -
                                        {{ $matkul->jam_akhir }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pengajar_id" class="form-label">Pengajar/Pengawas</label>
                            <select name="pengajar_id" id="pengajar_id" class="form-control">
                                <option value="">Pilih Pengajar/Pengawas</option>
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

    <template id="select-ujian">
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Ujian</label>
            <select name="jenis" id="jenis" class="form-control">
                <option value="">Pilih jenis</option>
                @foreach (config('services.ujian') as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </template>

    <template id="select-pertemuan">
        <div class="mb-3">
            <label for="pengajar_id" class="form-label">Asdos</label>
            <select name="pengajar_id" id="pengajar_id" class="form-control">
                <option value="">Pilih Asdos</option>
            </select>
        </div>
    </template>
@endsection

@push('js')
    <script>
        function generateCode() {
            $('#kode').val(generateRandomCode(6));
        }

        function getPengajar(){
            
        }

        $('#type').on('change', function() {
            $('.div-ujian').empty();
            if ($(this).val() == 'ujian') {
                $('.div-ujian').html($('#select-ujian').html());
            }
        });

        $(document).ready(function() {
            let table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.presensi.getJadwal', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "kode_matkul"
                    },
                    {
                        "data": "tgl"
                    },
                    {
                        "data": "matkul"
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
