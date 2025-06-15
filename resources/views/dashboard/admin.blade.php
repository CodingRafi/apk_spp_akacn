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
                        <div class="row">
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
                            <div class="col-md-2 mb-3">
                                <select name="semester" id="filter-semester" class="select2">
                                    <option value="">Pilih Semester</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="matkul" id="filter-matkul" class="select2">
                                    <option value="">Pilih Mata Kuliah</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                        <small class="text-danger">*Harap pilih semua filter</small>
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
            @can('view_kalender_akademik')
            <div class="col-md-6">
                <div class="card mt-3">
                    <div class="card-body">
                        <div id="calender"></div>
                    </div>
                </div>
            </div>
            @endcan
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
                    if (!point) {
                        return;
                    }

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
                text: 'Presensi'
            },
            xAxis: {
                categories: {!! json_encode($presensi->pluck('name')->toArray()) !!},
            },
            yAxis: {
                title: ''
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Total',
                data: {!! json_encode($presensi->pluck('y')->toArray()) !!}
            }]
        });
    </script>
    <script>
        Highcharts.chart('container-nilai', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Nilai',
            },
            xAxis: {
                categories: {!! json_encode($nilai->pluck('nama')->toArray()) !!},
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: '',
                gridLineWidth: 0
            },
            plotOptions: {
                series: {
                    colorByPoint: true
                },
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Total',
                data: {!! json_encode($nilai->pluck('y')->toArray()) !!},
            }]
        });
    </script>
    <script>
        $('#filter-tahun-ajaran, #filter-prodi').on('change', function() {
            getSemester();
            getMatkul();
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

        function getMatkul(matkul_id = null) {
            if ($('#filter-tahun-ajaran').val() != '' && $('#filter-prodi').val() != '') {
                $('#filter-matkul').empty().append(`<option value="">Pilih Mata Kuliah</option>`);
                $.ajax({
                    url: '{{ route('kelola-kuesioner.response.getMatkul') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        prodi_id: $('#filter-prodi').val(),
                        tahun_ajaran_id: $('#filter-tahun-ajaran').val(),
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-matkul').append(
                                `<option value="${e.id}">${e.kode} - ${e.nama}</option>`)
                        })

                        if (matkul_id != null) {
                            $('#filter-matkul').val(matkul_id);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }
    </script>
    @if (request('tahun_ajaran') != null && request('prodi') != null)
        <script>
            $(document).ready(function() {
                getSemester({{ request('semester') }});
                getMatkul({{ request('matkul') }});
            })
        </script>
    @endif
    @can('view_kalender_akademik')
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.17/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.17/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calender');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: @json($kalenderAkademik),
                eventDidMount: function(info) {
                    info.el.setAttribute("title", info.event.title);
                }
            });
            calendar.render();
        });
    </script>
    @endcan
@endpush
