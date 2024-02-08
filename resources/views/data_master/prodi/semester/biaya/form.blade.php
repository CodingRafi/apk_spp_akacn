@extends('mylayouts.main')

@section('container')
@php
    $page = isset($page) ? $page : 'form';
@endphp
<div class="content-wrapper">

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <form
                action="{{ isset($data) ? route('data-master.prodi.semester.biaya.update', ['prodi_id' => $prodi->id, 'semester_id' => $semester->id, 'tahun_ajaran_id' => $data->tahun_ajaran_id]) : route('data-master.prodi.semester.biaya.store', ['prodi_id' => $prodi->id, 'semester_id' => $semester->id]) }}"
                method="POST">
                @csrf
                @if (isset($data))
                @method('patch')
                @endif
                <div class="col-xl-12">
                    <!-- HTML5 Inputs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('data-master.prodi.semester.show', ['prodi_id' => $prodi->id, 'semester_id' => $semester->id]) }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Semester</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
                                    name="tahun_ajaran_id" {{ isset($data) ? 'disabled' : '' }}>
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach ($tahun_ajarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}" {{ isset($data) ? ($data->tahun_ajaran_id == $tahun_ajaran->id ? 'selected' : '') : '' }}>{{ $tahun_ajaran->nama }}</option>
                                    @endforeach
                                </select>
                                @error('tahun_ajaran_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input class="form-control @error('nominal') is-invalid @enderror" type="number"
                                    value="{{ isset($data) ? $data->nominal : old('nominal') }}" id="nominal"
                                    name="nominal" {{ $page == 'show' ? 'disabled' : '' }} />
                                @error('nominal')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                @if ($page == 'show')
                                {!! $data->ket !!}
                                @else
                                <textarea class="form-control summernote" id="ket"
                                    name="ket">{{ isset($data) ? $data->ket : old('ket') }}</textarea>
                                @endif
                                @error('ket')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="publish" class="form-label">Publish</label>
                                @if ($page == 'show')
                                <br>
                                {{ $data->publish ? 'Ya' : 'Tidak' }}
                                @else
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="publish"
                                        id="publish" {{ isset($data) ? ($data->publish ? 'checked' : '') : old('publish') }}>
                                </div>
                                @error('ket')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                                @endif
                            </div>
                            @if ($page == 'form')
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')

@endpush