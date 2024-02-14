@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form
                action="{{ isset($data) ? route('kelola-users.' . request('role') . '.update', $data->id) : route('kelola-users.' . request('role') . '.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($data))
                    @method('patch')
                @endif
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <a href="{{ route('kelola-users.index', request('role')) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} {{ request('role') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('users.' . request('role') . '.form')
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
