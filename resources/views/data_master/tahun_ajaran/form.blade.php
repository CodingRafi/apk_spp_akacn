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
                    <div class="f1">
                        <form
                            action="{{ isset($data) ? route('data-master.tahun-ajaran.update', $data->id) : route('data-master.tahun-ajaran.store') }}"
                            method="POST" class="form-tahun-ajaran">
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
                                    <label for="tgl_selesai" class="form-label">Tanggal Akhir</label>
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
                                            id="status"
                                            {{ isset($data) ? ($data->status ? 'checked' : '') : old('status') }}>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                    <button class="btn btn-primary" type="button" onclick="submitForm(this.form, this, setKurikulum)">Simpan</button>
                                </div>
                            </fieldset>

                            <fieldset data-id="2">

                            </fieldset>
                        </form>
                    </div>
                </div>
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
            '{{ isset($data) ? route('data-master.mata-kuliah.data', $data->id) : route('data-master.mata-kuliah.data', [':id']) }}'

        function generateName() {
            let tgl_mulai = $('#tgl_mulai').val().split('-')[0];
            let tgl_selesai = $('#tgl_selesai').val().split('-')[0];

            $('#nama').val(`${tgl_mulai}/${tgl_selesai}`)
        }

        $('#tgl_mulai').on('change', generateName);
        $('#tgl_selesai').on('change', generateName);

        function setTahunAjaran(){

        }
    </script>
    @include('mypartials.tab', ['form' => '.form-tahun-ajaran'])
@endpush
