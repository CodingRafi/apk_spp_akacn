@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Response Kuesioner</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <select name="tahun_ajaran_id" id="filter-tahun-ajaran" class="form-control">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjaran as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="prodi_id" id="filter-prodi" class="form-control">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodi as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="tahun_semester_id" id="filter-semester" class="form-control">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="tahun_matkul_id" id="filter-matkul" class="form-control">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                    </div>
                    <small class="text-danger">*Harap pilih tahun ajaran dan prodi</small>
                    <div class="table-responsive mt-3">
                        <table class="table" aria-label="table-response-kuesioner">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Semester</th>
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

    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="responseModalLabel">Detail</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <template id="template-choice">
        <div class="mb-3">
            <div class="pertanyaan">
                __PERTANYAAN__
            </div>
            <div class="d-flex" style="gap: 1rem;">
                @foreach (config('services.choice_kuesioner') as $i => $choice)
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="__ID_KUESIONER__-{{ $i }}" required
                        value="{{ $i }}" disabled>
                    <label class="form-check-label" for="__ID_KUESIONER__-{{ $i }}">
                        {{ $choice }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </template>
    <template id="template-input">
        <div class="mb-3">
            <div class="pertanyaan">
                __PERTANYAAN__
            </div>
            <div class="d-flex" style="gap: 1rem;">
                <input class="form-control" type="text" id="__ID_KUESIONER__" required value="__ANSWER__" disabled />
            </div>
        </div>
    </template>
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
                    url: '{{ route('kelola-kuesioner.response.data') }}',
                    data: function(d) {
                        d.tahun_ajaran_id = $('#filter-tahun-ajaran').val();
                        d.prodi_id = $('#filter-prodi').val();
                        d.semester_id = $('#filter-semester').val();
                        d.matkul_id = $('#filter-matkul').val();
                    }
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
                        "data": "semester"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "options"
                    },
                ],
                pageLength: 25,
                responsive: true,
            });
        });

        $('#filter-tahun-ajaran, #filter-prodi, #filter-semester, #filter-matkul').on('change', function() {
            table.ajax.reload();
        });

        $('#filter-tahun-ajaran, #filter-prodi').on('change', function() {
            getSemester();
            getMatkul();
        });

        function getSemester() {
            if ($('#filter-tahun-ajaran').val() != '' && $('#filter-prodi').val() != '') {
                $('#filter-semester').empty().append(`<option value="">Pilih Semester</option>`);
                $.ajax({
                    url: '{{ route('kelola-kuesioner.response.getSemester') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        prodi_id: $('#filter-prodi').val(),
                        tahun_ajaran_id: $('#filter-tahun-ajaran').val()
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-semester').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get semester');
                    }
                })
            }
        }

        function getMatkul() {
            if ($('#filter-tahun-ajaran').val() != '' && $('#filter-prodi').val() != '') {
                $('#filter-matkul').empty().append(`<option value="">Pilih Mata Kuliah</option>`);
                $.ajax({
                    url: '{{ route('kelola-kuesioner.response.getMatkul') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        prodi_id: $('#filter-prodi').val(),
                        tahun_ajaran_id: $('#filter-tahun-ajaran').val(),
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-matkul').append(
                                `<option value="${e.id}">${e.kode} - ${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }

        function showResponse(id) {
            $('#responseModal .modal-body').empty().append(`
                <div class="d-flex justify-content-center align-items-center m-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `)

            $('#responseModal').modal('show')

            $.ajax({
                url: '{{ route('kelola-kuesioner.response.show', ':id') }}'.replace(':id', id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    let response = ''

                    $.each(res.data, function(i, e) {
                        let template = $('#template-' + e.type).html();
                        if (e.type == 'input') {
                            response += template
                                .replace('__PERTANYAAN__', e.pertanyaan).replace('__ANSWER__', e.answer)
                        } else {
                            template = template.replace(/__ID_KUESIONER__/g, e.id)
                                .replace(/__PERTANYAAN__/g, e.pertanyaan)
                            let $template = $(template);
                            $template.find('input[value="' + e.answer + '"]').attr('checked', 'checked')
                            response += $template[0].outerHTML
                        }
                    })

                    $('#responseModal .modal-body').empty().append(response)
                },
                error: function(err) {
                    alert('Gagal get response');
                }
            })
        }
    </script>
@endpush
