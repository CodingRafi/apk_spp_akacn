@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Bimbingan Akademik</h5>
                </div>
                <div class="card-body">
                    @include('mahasiswa.bimbingan.table', [
                        'disabled' => true,
                        'mhs_id' => Auth::user()->id
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
