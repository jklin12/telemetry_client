@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->

<div class="panel panel-inverse">
    <div class="panel-body">
        <form action="" method="get" id="filter-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Station :</label>
                        <div class="col-md-7">
                            <select class="default-select2 form-control " id="station" name="station" required>
                                <option value="">-- Pilih Station --</option>
                                @forelse($station_list as $key => $value)
                                <option value="{{$value['station_id']}}">{{$value['station_name']}}</option>
                                @empty
                                <option value="">Data tidak ditemukan</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Display Interval :</label>
                        <div class="col-md-7">
                            <select class="form-control " id="interval" name="interval" required>
                                <option value="10">10 Minutes</option>
                                <option value="30">30 Minutes</option>
                                <option value="60">1 Hour</option>
                            </select>
                        </div>
                    </div>
                </div>-->
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Date : </label>
                        <div class="col-md-7">
                            <input id="reservationDate" type="text" name="date" class="form-control datepicker" value="{{ $filterDate }}" />
                        </div>
                    </div>
                </div>


                <!--<div class="text-muted f-w-600 mt-2 mt-sm-0">compared to <span id="daterange-prev-date">24 Mar-30 Apr 2020</span></div>-->
            </div>
        </form>

        <div id="container_chart"></div>
    </div>
</div>
@endsection



@push('scripts')
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    ///alert(Date())
    $(".default-select2").select2().on('select2:select', function(e) {
        var data = e.params.data;
        $('#filter-form').submit();
    });

    

    $(".datepicker").datepicker({
        format: 'yyyy-mm-dd',
        defaultDate: '<?php echo $filterDate ?>'
    }).on('changeDate', function(ev) {

        $('#filter-form').submit();
    });
    $('#interval').on('change', function(ev) {
        $('#filter-form').submit();
    });

    Highcharts.chart('container_chart', {
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: '',
            align: 'left',
            style: {
                display: 'none'
            }
        },
        xAxis: [{
            categories: <?php echo json_encode($data['label']) ?>,
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value} m',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            title: {
                text: '',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            opposite: true

        }, { // Secondary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Water Level',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} m',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            }

        }, { // Tertiary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Flow',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            labels: {
                format: '{value} m/s',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 80,
            verticalAlign: 'top',
            y: 55,
            floating: true,
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },
        series: [{
            name: 'Flow',
            type: 'spline',
            yAxis: 1,
            data: <?php echo json_encode($data['flow']) ?>,
            tooltip: {
                valueSuffix: ' m/s'
            }

        }, {
            name: 'Water Level',
            type: 'spline',
            yAxis: 2,
            data: <?php echo json_encode($data['water_level']) ?>,
            tooltip: {
                valueSuffix: ' m'
            }
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        floating: false,
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom',
                        x: 0,
                        y: 0
                    },
                    yAxis: [{
                        labels: {
                            align: 'right',
                            x: 0,
                            y: -6
                        },
                        showLastLabel: false
                    }, {
                        labels: {
                            align: 'left',
                            x: 0,
                            y: -6
                        },
                        showLastLabel: false
                    }, {
                        visible: false
                    }]
                }
            }]
        }
    });
</script>


@endpush