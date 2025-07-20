@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Total Mengajar</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ $totalMengajar }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Jadwal Menunggu Verifikasi</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ $totalMengajarMenunggu }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Jadwal Disetujui</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ $totalMengajarDisetujui }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Jadwal Ditolak</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ $totalMengajarDitolak }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @can('view_kalender_akademik')
                @include('dashboard.partials.kalender')
            @endcan
        </div>
    </div>
@endsection
