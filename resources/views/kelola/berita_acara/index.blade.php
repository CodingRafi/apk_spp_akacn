@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Berita Acara {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="tahun_ajaran_id" class="select2 mb-3" onchange="get_semester()">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="prodi_id" class="form-control mb-3" onchange="get_semester()">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_semester_id" class="form-control mb-3">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <small class="text-danger">*Harap pilih semua filter untuk melihat mata kuliah</small>
                    <div class="table-responsive mt-3">
                        <table class="table table-matkul" id="table-matkul" aria-label="Data matkul">
                            <thead>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Rombel</th>
                                    <th>Aksi</th>
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
@endsection

@push('js')
    <script>
        function get_semester() {
            const tahun_ajaran_id = $('#tahun_ajaran_id').val();
            const prodi_id = $('#prodi_id').val();

            if (tahun_ajaran_id != '' && prodi_id != '') {
                $('#tahun_semester_id').empty().append(`<option value="">Pilih Semester</option>`);
                $.ajax({
                    url: "{{ route('kelola-presensi.rekap.getSemester', ['tahun_ajaran_id' => ':tahun_ajaran_id']) }}".replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: "json",
                    data: {
                        prodi_id: prodi_id
                    },
                    success: function(res) {
                        res.data.forEach(e => {
                            $('#tahun_semester_id').append(`<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function() {
                        alert('Gagal get semester')
                    }
                })
            }
        }

        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.berita-acara.data') }}',
                    data: function(p) {
                        p.prodi_id = $('#prodi_id').val();
                        p.tahun_semester_id = $('#tahun_semester_id').val();
                        p.tahun_ajaran_id = $('#tahun_ajaran_id').val();
                    }
                },
                columns: [
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "rombel"
                    },
                    {
                        "data": "options"
                    },
                ],
                pageLength: 25,
                responsive: true,
            });
        });

        $('#prodi_id, #tahun_semester_id, #tahun_ajaran_id').on('change', function() {
            table.ajax.reload();
        });
    </script>
@endpush
