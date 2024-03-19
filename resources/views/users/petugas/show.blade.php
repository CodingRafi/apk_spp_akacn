@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-users.index', ['role' => 'petugas']) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Detail Petugas</h5>
                    </div>
                </div>
                <div class="card-body">
                    @include('users.petugas.form', [
                        'disabled' => true,
                        'role' == 'petugas',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
