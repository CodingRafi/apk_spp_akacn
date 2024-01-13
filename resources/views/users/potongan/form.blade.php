@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ route('users.potongan.store', ['role' => request('role'), 'id' => request('id'), 'semester_id' => request('semester_id')]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('users.potongan.show', ['role' => request('role'), 'id' => request('id'), 'semester_id' => request('semester_id')]) }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="text-capitalize mb-0">Set Potongan
                                    {{ $semester->nama }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="potongan_id" class="form-label">Potongan</label>
                                    <select class="form-select select2 @error('potongan_id') is-invalid @enderror"
                                        name="potongan_id[]" multiple>
                                        <option value="">Pilih Potongan</option>
                                        @foreach ($potongans as $potongan)
                                            <option value="{{ $potongan->id }}">
                                                {{ $potongan->nama }}({{ formatRupiah($potongan->nominal) }})</option>
                                        @endforeach
                                    </select>
                                    @error('potongan_id')
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
