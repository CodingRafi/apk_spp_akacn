@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('data-master.tahun-ajaran.index') }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Tahun Ajaran</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($data) ? route('data-master.tahun-ajaran.update', $data->id) : route('data-master.tahun-ajaran.store') }}"
                        method="POST" class="form-tahun-ajaran f1" data-status="{{ isset($data) ? 'true' : 'false' }}">
                        @csrf
                        @if (isset($data))
                            @method('patch')
                        @endif
                        <fieldset data-id="1">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input class="form-control" type="text" id="nama" readonly name="nama"
                                    value="{{ isset($data) ? $data->nama : (old('tgl_mulai') ? explode('-', old('tgl_mulai'))[0] . '/' . explode('-', old('tgl_selesai'))[0] : '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                                <input class="form-control @error('tgl_mulai') is-invalid @enderror" type="date"
                                    value="{{ isset($data) ? $data->tgl_mulai : old('tgl_mulai') }}" id="tgl_mulai"
                                    name="tgl_mulai" />
                                @error('tgl_mulai')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                                <input class="form-control @error('tgl_selesai') is-invalid @enderror" type="date"
                                    value="{{ isset($data) ? $data->tgl_selesai : old('tgl_selesai') }}" id="tgl_selesai"
                                    name="tgl_selesai" />
                                @error('tgl_selesai')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="status"
                                        id="status" {{ isset($data) ? ($data->status ? 'checked' : '') : old('status') }}
                                        value="1">
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary btn-next btn-selanjutnya-1" type="button"
                                    onclick="submitForm(this.form, this, setTahunAjaran)">Simpan dan lanjutkan</button>
                            </div>
                        </fieldset>

                        <fieldset data-id="2">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Semester</h5>
                                <div class="d-flex justify-content-center align-items-center" style="gap: 1rem;">
                                    <button class="btn btn-primary" type="button" onclick="get()">Get NEO Feeder</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="addForm('{{ route('data-master.semester.store') }}', 'Tambah', '#semester', getLastSemester)">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-semester">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Semester</th>
                                            <th>Status</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Akhir</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="f1-buttons mb-3 mt-3">
                                <button type="button" class="btn float-start btn-secondary btn-previous">Kembali</button>
                                <button type="button" class="btn btn-selesai btn-primary">Selesai</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="semester" tabindex="-1" aria-labelledby="semesterLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="semesterLabel">Tambah Semester</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="tahun_ajaran_id">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="nama" name="nama" />
                        </div>
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input class="form-control" type="number" id="semester" name="semester" readonly />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_mulai" class="form-label">Tanggal mulai</label>
                            <input class="form-control" type="date" id="tgl_mulai_semester" name="tgl_mulai" />
                        </div>
                        <div class="mb-3">
                            <label for="tgl_selesai" class="form-label">Tanggal selesai</label>
                            <input class="form-control" type="date" id="tgl_selesai_semester" name="tgl_selesai" />
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="status"
                                    id="status" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableSemester.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let status_next = false;
        let tahun_ajaran;
        let tableSemester;
        let url_update = '{{ route('data-master.tahun-ajaran.update', [':id']) }}';
        let url_semester =
            '{{ isset($data) ? route('data-master.semester.data', $data->id) : route('data-master.semester.data', [':id']) }}'
        let url_last_semester =
            '{{ isset($data) ? route('data-master.semester.getLastSemester', $data->id) : route('data-master.semester.getLastSemester', [':id']) }}'

        function generateName() {
            let tgl_mulai = $('#tgl_mulai').val().split('-')[0];
            let tgl_selesai = $('#tgl_selesai').val().split('-')[0];

            $('#nama').val(`${tgl_mulai}/${tgl_selesai}`)
        }

        $('#tgl_mulai').on('change', generateName);
        $('#tgl_selesai').on('change', generateName);

        function getLastSemester() {
            $.ajax({
                url: url_last_semester,
                method: 'get',
                success: function(data) {
                    $('input#semester').val(parseInt(data.semester) + 1);
                },
                error: function() {
                    alert('Gagal get last semester');
                }
            })
        }

        $(document).ready(function() {
            tableSemester = $('.table-semester').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: url_semester,
                },
                columns: [{
                        data: 'nama'
                    },
                    {
                        data: 'semester'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'tgl_mulai'
                    },
                    {
                        data: 'tgl_selesai'
                    },
                    {
                        data: 'options',
                        searchable: false,
                        sortable: false
                    },
                ]
            });
        })

        function setTahunAjaran(data) {
            let status = $('.form-tahun-ajaran').attr('data-status');
            $('.form-tahun-ajaran')
                .attr('action', url_update.replace(':id', data.id))
                .attr('data-status', 'true')
                .append('<input type="hidden" name="_method" value="patch">');
            $('[name="tahun_ajaran_id"]').val(data.id);
            kurikulum = data;
            console.log(status)
            if (status == 'false') {
                $('.btn-selanjutnya-1').trigger('click');
            }

            // Semester
            url_semester = url_semester.replace(':id', data.id);
            tableSemester.ajax.url(url_semester);
            tableSemester.ajax.reload()

            $(".form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-editor")
                .removeClass("is-invalid");
            $(".invalid-feedback").remove();
        }

        $('.btn-selesai').on('click', function() {
            window.location.href = '{{ route('data-master.tahun-ajaran.index') }}'
        })

        function get() {
            $.LoadingOverlay("show");
            $.ajax({
                url: '{{ route('data-master.semester.get-neo-feeder', ['tahun_ajaran_id' => request('tahun_ajaran')]) }}',
                success: function(res) {
                    showAlert(res.output, 'success')
                    $.LoadingOverlay("hide");
                    tableSemester.ajax.reload();
                },
                error: function(err) {
                    $.LoadingOverlay("hide");
                    showAlert(err.responseJSON.output, 'error')
                }
            })
        }
    </script>
    @include('mypartials.tab', ['form' => '.form-tahun-ajaran'])
@endpush
