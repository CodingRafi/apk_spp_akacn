@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('patch')
                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <a href="{{ route('roles.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                                <h5 class="mb-0">Ubah Role</h5>
                            </div>
                            
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="permission" class="form-label">Permission</label>
                                    <select name="permission[]" id="permission" class="select2" multiple>
                                        @foreach ($permissions as $permission)
                                            <option value="{{ $permission->id }}" {{ in_array($permission->id, $rolePermissions) ? 'selected' : '' }}>
                                                {{ str_replace('_', ' ', $permission->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('permission')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
