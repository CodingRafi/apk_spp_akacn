@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">KRS</h5>
                </div>
                <div class="card-body">
                    @include('mahasiswa.krs.table')
                </div>
            </div>
        </div>
    </div>
@endsection
