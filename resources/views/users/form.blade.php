@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form
                    action="{{ isset($data) ? route('users.update', ['role' => request('role'), 'id' => $data->id]) : route('users.store', ['role' => request('role')]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($data))
                        @method('patch')
                    @endif
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <a href="{{ route('users.index', request('role')) }}"><i
                                    class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} {{ request('role') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if (request('role') == 'mahasiswa')
                                @include('users.partials.mahasiswa.form')
                            @endif
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
