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
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Tagihan</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ formatRupiah($tagihan->tagihan) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Semester</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ count($krs) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">Jumlah SKS</h5>
                            <p class="card-text" style="font-size: 1.6rem;">{{ $krs->sum('jml_sks_diambil') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="container-masa-studi"></div>
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

@push('js')
    <script>
        Highcharts.chart('container-masa-studi', {
            title: {
                text: 'Masa Studi',
            },

            yAxis: {
                title: ''
            },

            xAxis: {
                categories: {!! json_encode($krs->pluck('semester')->toArray()) !!},
            },

            credits: {
                enabled: false
            },

            series: [{
                name: 'Total',
                data: {!! json_encode(array_map('intval', $krs->pluck('jml_sks_diambil')->toArray())) !!}
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        });
    </script>
@endpush
