@extends('mylayouts.main')

@push('css')
<style>
    .highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>
@endpush

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card" style="border-top: 10px solid #1E88D7;">
                <div class="card-body">
                    <form action="" class="row">
                        <div class="col-md-3 mb-3">
                            <select class="form-select" id="filter-prodi" name="prodi">
                                <option value="" selected>Pilih Prodi</option>
                                {{-- @foreach ($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select class="form-select" id="filter-tahun-ajaran" name="tahun_ajaran">
                                <option value="" selected>Pilih Tahun Ajaran</option>
                                {{-- @foreach ($tahun_ajarans as $tahun_ajaran)
                                <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                    <div id="container-pembayaran"></div>
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- @push('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    Highcharts.chart('container-pembayaran', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Pembayaran',
        align: 'left'
    },
    xAxis: {
        categories: ['Pending', 'Diterima', 'Ditolak'],
        crosshair: true,
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Pembayaran'
        }
    },
    series: [
        {
            name: 'Jumlah Pembayaran',
            data: {!! json_encode($data) !!}
        }
    ],
    credits: {
        enabled: false
    },
});
</script>
@endpush --}}