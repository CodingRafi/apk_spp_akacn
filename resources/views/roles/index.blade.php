@extends('mylayouts.main')

@section('tambahancss')
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="">Roles</h5>
                </div>
                <div class="container-fluid" style="height: 65vh;overflow: auto;">
                    <div id="accordionIcon" class="accordion mt-3 accordion-without-arrow">
                        @foreach ($roles as $key => $role)
                            <div class="accordion-item card mb-3">
                                <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionIconOne">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#accordionIcon-{{ $loop->iteration }}"
                                        aria-controls="accordionIcon-{{ $loop->iteration }}" style="text-transform: capitalize;">
                                        {{ $loop->iteration }}. Role {{ str_replace("_", " ", $role->name) }}
                                        <i class='bx bx-chevron-right' style="position: absolute;right: 1rem;font-size: 1.7rem;"></i> 
                                    </button>
                                </h2>

                                <div id="accordionIcon-{{ $loop->iteration }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionIcon">
                                    <div class="accordion-body">
                                        <div class="container-fluid">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="card-header ps-0" style="text-transform: capitalize;">Hak akses untuk role {{ str_replace("_", " ", $role->name) }}
                                                </h5>
                                                @can('edit_roles')
                                                    <a href="{{ route('roles.edit', $role->id) }}"
                                                        class="btn btn-warning" style="margin-right: 10px;">Update</a>
                                                @endcan
                                            </div>
                                        </div>
                                        {{-- <p>{{ $rolePermission->name }}</p> --}}
                                        <div class="container-fluid">
                                            <div class="row flex-wrap">
                                                @foreach ($rolePermissions[$key] as $rolePermission)
                                                    <div class="col-md-3 mb-2 mt-2">
                                                        {{ str_replace('_', ' ', $rolePermission->name) }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection