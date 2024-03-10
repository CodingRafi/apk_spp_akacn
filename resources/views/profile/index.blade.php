@extends('mylayouts.main')

@section('container')
    @php
        $role = getRole()->name;
    @endphp
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4" style="border-top: 10px solid #1e88d7">
                        <h5 class="card-header">Profil</h5>
                        <form
                            action="{{ isset($data) ? route('kelola-users.' . $role . '.update', $data->id) : route('kelola-users.' . $role . '.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($data))
                                @method('patch')
                            @endif
                            <div class="container-fluid pb-4">
                                @include('users.' . getRole()->name . '.form', [
                                    'page' => 'profile',
                                ])
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
