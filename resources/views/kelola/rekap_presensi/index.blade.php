@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Rekap Presensi {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="prodi_id" class="form-control mb-3" onchange="get_rombel();get_matkul();get_semester();">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_matkul_id" class="form-control mb-3" onchange="get_rombel()">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="rombel_id" class="form-control mb-3" onchange="get_presensi()">
                                <option value="">Pilih Rombel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="tahun_semester_id" class="form-control mb-3" onchange="get_presensi()">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                    </div>
                    <small class="text-danger">*Harap pilih semua filter untuk melihat rekap</small>
                    <div class="table-responsive mt-3">
                        <table class="table table-presensi" id="table-presensi">
                            <thead>
                                <tr>
                                    <td>Nama</td>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>4</td>
                                    <td>5</td>
                                    <td>6</td>
                                    <td>7</td>
                                    <td>UTS</td>
                                    <td>8</td>
                                    <td>9</td>
                                    <td>10</td>
                                    <td>11</td>
                                    <td>12</td>
                                    <td>13</td>
                                    <td>14</td>
                                    <td>UAS</td>
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
@endsection

@push('js')
    <script>
        function generate_table(data) {
            let table = '';

            data.forEach(e => {
                table +=
                    `<tr>
                    <td>${e.name}</td>`;

                e.presensi.forEach(el => {
                    table += `<td>${el.jadwal_id ? (el.status ?? '-') : ''}</td>`;
                })

                table += `</tr>`;
            });

            $('.table-presensi tbody').html(table);
        }

        function get_rombel() {
            $('#rombel_id').empty().append(`<option value="">Pilih Rombel</option>`);
            $.ajax({
                url: "{{ route('kelola-presensi.rekap.getRombel') }}",
                type: 'GET',
                dataType: "json",
                data: {
                    tahun_matkul_id: $('#tahun_matkul_id').val(),
                },
                success: function(res) {
                    res.data.forEach(e => {
                        $('#rombel_id').append(`<option value="${e.id}">${e.nama}</option>`)
                    })
                },
                error: function() {
                    alert('Gagal get rombel')
                }
            })
        }

        function get_matkul() {
            $('#tahun_matkul_id').empty().append(`<option value="">Pilih Mata Kuliah</option>`);
            $.ajax({
                url: "{{ route('kelola-presensi.rekap.getMatkul', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                type: 'GET',
                dataType: "json",
                data: {
                    prodi_id: $('#prodi_id').val()
                },
                success: function(res) {
                    res.data.forEach(e => {
                        $('#tahun_matkul_id').append(`<option value="${e.id}">${e.nama}</option>`)
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

        function get_presensi() {
            $('.table-presensi tbody').empty();
            if ($('#rombel_id').val() != '' && $('#tahun_semester_id').val() != '') {
                $('.table-presensi tbody').append(`<tr>
                                                    <td colspan="17" class="text-center py-4">
                                                        <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </td>
                                                </tr>`);
                $.ajax({
                    url: "{{ route('kelola-presensi.rekap.getPresensi', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        tahun_semester_id: $('#tahun_semester_id').val(),
                        rombel_id: $('#rombel_id').val(),
                        tahun_matkul_id: $('#tahun_matkul_id').val(),
                    },
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
