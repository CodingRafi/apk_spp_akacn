@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    class="form"
                    action="{{ isset($data) ? route('data-master.potongan.update', $data->id) : route('data-master.potongan.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('data-master.potongan.index') }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Potongan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input class="form-control @error('nama') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->nama : old('nama') }}" id="nama"
                                        name="nama" />
                                    @error('nama')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="nominal" class="form-label">Nominal</label>
                                    <input class="form-control @error('nominal') is-invalid @enderror" type="text"
                                        value="{{ isset($data) ? $data->nominal : old('nominal') }}" id="nominal"
                                        name="nominal" />
                                    @error('nominal')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="ket" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('ket') is-invalid @enderror" id="ket" rows="3" name="ket">{{ isset($data) ? $data->ket : old('ket') }}</textarea>
                                    @error('ket')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="prodi_id" class="form-label">Prodi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-select"
                                        {{ isset($data) ? ($total_mhs > 0 ? 'disabled' : '') : '' }}>
                                        <option value="">Pilih Prodi</option>
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi->id }}"
                                                {{ isset($data) ? ($data->prodi_id == $prodi->id ? 'selected' : '') : '' }}>
                                                {{ $prodi->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select"
                                        {{ isset($data) ? ($total_mhs > 0 ? 'disabled' : '') : '' }}>
                                        <option value="">Pilih Tahun AJaran</option>
                                        @foreach ($tahun_ajarans as $tahun_ajaran)
                                            <option value="{{ $tahun_ajaran->id }}"
                                                {{ isset($data) ? ($data->tahun_ajaran_id == $tahun_ajaran->id ? 'selected' : '') : '' }}>
                                                {{ $tahun_ajaran->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('tahun_ajaran_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-select"
                                        {{ isset($data) ? ($total_mhs > 0 ? 'disabled' : '') : '' }}>
                                        <option value="">Pilih Semester</option>
                                        @if (isset($data))
                                            @foreach ($semester as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $item->id == $data->semester_id ? 'selected' : '' }}>
                                                    {{ $item->nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('semester_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                    <button class="btn btn-primary btn-submit" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @if (isset($data) && $total_mhs > 0)
        <script>
            $('.btn-submit').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah anda yakin ingin mengedit data ini?',
                    text: "Data ini telah di set pada mahasiswa",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'

                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.form').submit();
                    }
                })
            })
        </script>
    @endif
    <script>
        $('#prodi_id').on('change', function() {
            $('#semester_id').empty().append('<option value="">Pilih Semester</option>');
            if ($(this).val()) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('data-master.potongan.getSemester', ':id') }}".replace(':id', $(this)
                        .val()),
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#semester_id').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        showAlert(err.responseJSON.message, 'error');
                    }
                })
            }
        })
    </script>
@endpush
