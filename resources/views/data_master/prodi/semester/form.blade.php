@extends('mylayouts.main')

@section('container')
<div class="content-wrapper">
    
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="row">
            <form
                action="{{ isset($data) ? route('data-master.prodi.semester.update', ['prodi_id' => $prodi_id, 'semester_id' => $data->id]) : route('data-master.prodi.semester.store', $prodi_id) }}"
                method="POST">
                @csrf
                @if (isset($data))
                @method('patch')
                @endif
                <div class="col-xl-12">
                    <!-- HTML5 Inputs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('data-master.prodi.show', $prodi_id) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Semester</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input class="form-control @error('nama') is-invalid @enderror" type="text"
                                    value="{{ isset($data) ? $data->nama : old('nama') }}" id="nama" name="nama" />
                                @error('nama')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection