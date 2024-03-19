@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Pembayaran</h5>
                    <a href="{{ route('pembayaran.export') }}" class="btn btn-primary">Export</a>
                </div>
                <div class="card-body">
                    @include('mahasiswa.pembayaran.table')
                </div>
            </div>
        </div>
    </div>
@endsection
