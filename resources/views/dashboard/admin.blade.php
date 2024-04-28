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
            <div class="row mb-3">
                @foreach ($users as $user)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title text-capitalize" style="font-size: 1.2rem">
                                    {{ $user->name }}
                                </h5>
                                <p class="card-text" style="font-size: 1.6rem;">{{ $user->users_count }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <select name="prodi" id="filter-prodi" class="select2">
                                    <option value="">Pilih Prodi</option>
                                    @foreach ($prodis as $prodi)
                                        <option value="{{ $prodi->id }}"
                                            {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="tahun_ajaran" id="filter-tahun-ajaran" class="select2">
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach ($tahunAjaran as $tahun)
                                        <option value="{{ $tahun->id }}"
                                            {{ request('tahun_ajaran') == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="semester" id="filter-semester" class="select2">
                                    <option value="">Pilih Semester</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="container-pembayaran"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="container-krs"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="container-presensi"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="container-nilai"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function(H) {
            H.seriesTypes.pie.prototype.animate = function(init) {
                const series = this,
                    chart = series.chart,
                    points = series.points,
                    {
                        animation
                    } = series.options,
                    {
                        startAngleRad
                    } = series;

                function fanAnimate(point, startAngleRad) {
                    const graphic = point.graphic,
                        args = point.shapeArgs;

                    if (graphic && args) {

                        graphic
                            // Set inital animation values
                            .attr({
                                start: startAngleRad,
                                end: startAngleRad,
                                opacity: 1
                            })
                            // Animate to the final position
                            .animate({
                                start: args.start,
                                end: args.end
                            }, {
                                duration: animation.duration / points.length
                            }, function() {
                                // On complete, start animating the next point
                                if (points[point.index + 1]) {
                                    fanAnimate(points[point.index + 1], args.end);
                                }
                                // On the last point, fade in the data labels, then
                                // apply the inner size
                                if (point.index === series.points.length - 1) {
                                    series.dataLabelsGroup.animate({
                                            opacity: 1
                                        },
                                        void 0,
                                        function() {
                                            points.forEach(point => {
                                                point.opacity = 1;
                                            });
                                            series.update({
                                                enableMouseTracking: true
                                            }, false);
                                            chart.update({
                                                plotOptions: {
                                                    pie: {
                                                        innerSize: '40%',
                                                        borderRadius: 8
                                                    }
                                                }
                                            });
                                        });
                                }
                            });
                    }
                }

                if (init) {
                    // Hide points on init
                    points.forEach(point => {
                        point.opacity = 0;
                    });
                } else {
                    fanAnimate(points[0], startAngleRad);
                }
            };
        }(Highcharts));
    </script>
    <script>
        Highcharts.chart('container-pembayaran', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Pembayaran',
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    borderWidth: 2,
                    cursor: 'pointer',
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                animation: {
                    duration: 2500
                },
                colorByPoint: true,
                name: 'Total',
                data: {!! json_encode($pembayaran) !!}
            }]
        });
    </script>
    <script>
        Highcharts.chart('container-krs', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Kartu Rencana Studi',
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    borderWidth: 2,
                    cursor: 'pointer',
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                animation: {
                    duration: 2500
                },
                colorByPoint: true,
                name: 'Total',
                data: {!! json_encode($krs) !!}
            }]
        });
    </script>
    <script>
        Highcharts.chart('container-presensi', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Corn vs wheat estimated production for 2020',
                align: 'left'
            },
            subtitle: {
                text: 'Source: <a target="_blank" ' +
                    'href="https://www.indexmundi.com/agriculture/?commodity=corn">indexmundi</a>',
                align: 'left'
            },
            xAxis: {
                categories: ['USA', 'China', 'Brazil', 'EU', 'India', 'Russia'],
                crosshair: true,
                accessibility: {
                    description: 'Countries'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '1000 metric tons (MT)'
                }
            },
            tooltip: {
                valueSuffix: ' (1000 MT)'
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                    name: 'Corn',
                    data: [406292, 260000, 107000, 68300, 27500, 14500]
                },
                {
                    name: 'Wheat',
                    data: [51086, 136000, 5500, 141000, 107180, 77000]
                }
            ]
        });
    </script>
    <script>
        Highcharts.chart('container-nilai', {
            chart: {
                type: 'bar'
            },
            title: {
                align: 'Nilai',
            },
            xAxis: {
                type: ''
            },
            yAxis: {
                title: ''

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                }
            },
            series: [{
                name: 'Browsers',
                colorByPoint: true,
                data: [{
                        name: 'Chrome',
                        y: 63.06,
                        drilldown: 'Chrome'
                    },
                    {
                        name: 'Safari',
                        y: 19.84,
                        drilldown: 'Safari'
                    },
                    {
                        name: 'Firefox',
                        y: 4.18,
                        drilldown: 'Firefox'
                    },
                    {
                        name: 'Edge',
                        y: 4.12,
                        drilldown: 'Edge'
                    },
                    {
                        name: 'Opera',
                        y: 2.33,
                        drilldown: 'Opera'
                    },
                    {
                        name: 'Internet Explorer',
                        y: 0.45,
                        drilldown: 'Internet Explorer'
                    },
                    {
                        name: 'Other',
                        y: 1.582,
                        drilldown: null
                    }
                ]
            }],
            drilldown: {
                breadcrumbs: {
                    position: {
                        align: 'right'
                    }
                },
                series: [{
                        name: 'Chrome',
                        id: 'Chrome',
                        data: [
                            [
                                'v65.0',
                                0.1
                            ],
                            [
                                'v64.0',
                                1.3
                            ],
                            [
                                'v63.0',
                                53.02
                            ],
                            [
                                'v62.0',
                                1.4
                            ],
                            [
                                'v61.0',
                                0.88
                            ],
                            [
                                'v60.0',
                                0.56
                            ],
                            [
                                'v59.0',
                                0.45
                            ],
                            [
                                'v58.0',
                                0.49
                            ],
                            [
                                'v57.0',
                                0.32
                            ],
                            [
                                'v56.0',
                                0.29
                            ],
                            [
                                'v55.0',
                                0.79
                            ],
                            [
                                'v54.0',
                                0.18
                            ],
                            [
                                'v51.0',
                                0.13
                            ],
                            [
                                'v49.0',
                                2.16
                            ],
                            [
                                'v48.0',
                                0.13
                            ],
                            [
                                'v47.0',
                                0.11
                            ],
                            [
                                'v43.0',
                                0.17
                            ],
                            [
                                'v29.0',
                                0.26
                            ]
                        ]
                    },
                    {
                        name: 'Firefox',
                        id: 'Firefox',
                        data: [
                            [
                                'v58.0',
                                1.02
                            ],
                            [
                                'v57.0',
                                7.36
                            ],
                            [
                                'v56.0',
                                0.35
                            ],
                            [
                                'v55.0',
                                0.11
                            ],
                            [
                                'v54.0',
                                0.1
                            ],
                            [
                                'v52.0',
                                0.95
                            ],
                            [
                                'v51.0',
                                0.15
                            ],
                            [
                                'v50.0',
                                0.1
                            ],
                            [
                                'v48.0',
                                0.31
                            ],
                            [
                                'v47.0',
                                0.12
                            ]
                        ]
                    },
                    {
                        name: 'Internet Explorer',
                        id: 'Internet Explorer',
                        data: [
                            [
                                'v11.0',
                                6.2
                            ],
                            [
                                'v10.0',
                                0.29
                            ],
                            [
                                'v9.0',
                                0.27
                            ],
                            [
                                'v8.0',
                                0.47
                            ]
                        ]
                    },
                    {
                        name: 'Safari',
                        id: 'Safari',
                        data: [
                            [
                                'v11.0',
                                3.39
                            ],
                            [
                                'v10.1',
                                0.96
                            ],
                            [
                                'v10.0',
                                0.36
                            ],
                            [
                                'v9.1',
                                0.54
                            ],
                            [
                                'v9.0',
                                0.13
                            ],
                            [
                                'v5.1',
                                0.2
                            ]
                        ]
                    },
                    {
                        name: 'Edge',
                        id: 'Edge',
                        data: [
                            [
                                'v16',
                                2.6
                            ],
                            [
                                'v15',
                                0.92
                            ],
                            [
                                'v14',
                                0.4
                            ],
                            [
                                'v13',
                                0.1
                            ]
                        ]
                    },
                    {
                        name: 'Opera',
                        id: 'Opera',
                        data: [
                            [
                                'v50.0',
                                0.96
                            ],
                            [
                                'v49.0',
                                0.82
                            ],
                            [
                                'v12.1',
                                0.14
                            ]
                        ]
                    }
                ]
            }
        });
    </script>
    <script>
        $('#filter-tahun-ajaran, #filter-prodi').on('change', function() {
            getSemester();
        });

        function getSemester(semester_id = null) {
            if ($('#filter-tahun-ajaran').val() != '' && $('#filter-prodi').val() != '') {
                $('#filter-semester').empty().append(`<option value="">Pilih Semester</option>`);
                $.ajax({
                    url: '{{ route('kelola-kuesioner.response.getSemester') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        prodi_id: $('#filter-prodi').val(),
                        tahun_ajaran_id: $('#filter-tahun-ajaran').val()
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-semester').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })

                        if (semester_id != null) {
                            $('#filter-semester').val(semester_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get semester');
                    }
                })
            }
        }
    </script>
    @if (request('tahun_ajaran') != null && request('prodi') != null)
        <script>
            $(document).ready(function() {
                getSemester({{ request('semester') }});
            })
        </script>
    @endif
@endpush
