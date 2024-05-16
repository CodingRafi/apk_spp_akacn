@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-nilai.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Nilai</h5>
                    </div>
                    @if (Auth::user()->hasRole('admin'))
                        <button class="btn btn-primary" onclick="getDataNeoFeeder()">Get Neo Feeder</button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="prodi_id" class="form-control mb-3" onchange="get_matkul();get_semester();">
                                <option value="">Pilih Program Studi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_matkul_id" class="form-control mb-3 select2">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_semester_id" class="form-control mb-3">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <small class="text-danger">*Harap pilih semua filter</small>
                    <div class="table-responsive mt-3">
                        <table class="table" aria-label="Data rombel">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
                    url: '{{ route('kelola-nilai.getRombel', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}',
                    data: function(p) {
                        p.tahun_semester_id = $('#tahun_semester_id').val();
                        p.tahun_matkul_id = $('#tahun_matkul_id').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "nama"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });

        $('#tahun_matkul_id, #tahun_semester_id').on('change', function() {
            table.ajax.reload();
        });

        function get_matkul() {
            $('#tahun_matkul_id').empty().append(`<option value="">Pilih Mata Kuliah</option>`);
            $.ajax({
                url: "{{ route('kelola-nilai.getMatkul', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                type: 'GET',
                dataType: "json",
                data: {
                    prodi_id: $('#prodi_id').val()
                },
                success: function(res) {
                    res.data.forEach(e => {
                        $('#tahun_matkul_id').append(`<option value="${e.id}">${e.kode} - ${e.nama}</option>`)
                    })
                },
                error: function() {
                    alert('Gagal get matkul')
                }
            })
        }

        function get_semester() {
            $('#tahun_semester_id').empty().append(`<option value="">Pilih Semester</option>`);
            $.ajax({
                url: "{{ route('kelola-presensi.rekap.getSemester', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                type: 'GET',
                dataType: "json",
                data: {
                    prodi_id: $('#prodi_id').val()
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
    </script>
    @if (Auth::user()->hasRole('admin'))
        @include('kelola.nilai.neo_feeder.get')
    @endif
@endpush
