@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

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
                                <option value="">-- Pilih Profile --</option>
                                @forelse($station_list as $key => $value)
                                <option value="{{$value['station_id']}}">{{$value['station_name']}}</option>
                                @empty
                                <option value="">Data tidak ditemukan</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
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
                </div>
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>

<script>
    ///alert(Date())
    $(".default-select2").select2().on('select2:select', function(e) {
        var data = e.params.data;
        $('#filter-form').submit();
    }).select2('val', '<?php echo $filterStation ?>');

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
            type: 'area',
            zoomType: 'xy'
        },
        title: {
            text: 'ETH-BTC Market Depth',
            style: {
                display: 'none'
            }
        },
        xAxis: {
            minPadding: 0,
            maxPadding: 0,
            plotLines: [{
                color: '#888',
                value: 0.1523,
                width: 1,
                label: {
                    text: '',
                    rotation: 90,
                    style: {
                        display: 'none'
                    }
                }
            }],
            title: {
                text: 'Time',
                style: {
                        display: 'none'
                    }
            }
        },
        yAxis: [{
            lineWidth: 1,
            gridLineWidth: 1,
            title: null,
            tickWidth: 1,
            tickLength: 5,
            tickPosition: 'inside',
            labels: {
                align: 'left',
                x: 8
            }
        }, {
            opposite: true,
            linkedTo: 0,
            lineWidth: 1,
            gridLineWidth: 0,
            title: null,
            tickWidth: 1,
            tickLength: 5,
            tickPosition: 'inside',
            labels: {
                align: 'right',
                x: -8
            }
        }],
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillOpacity: 0.2,
                lineWidth: 1,
                step: 'center'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size=10px;">Time: {point.key}</span><br/>',
            valueDecimals: 2
        },
        series: [{
            name: 'Working Rainfall',
            data: [
                [
                    0.1524,
                    0.948665
                ],
                [
                    0.1539,
                    35.510715
                ],
                [
                    0.154,
                    39.883437
                ],
                [
                    0.1541,
                    40.499661
                ],
                [
                    0.1545,
                    43.262994000000006
                ],
                [
                    0.1547,
                    60.14799400000001
                ],
                [
                    0.1553,
                    60.30799400000001
                ],
                [
                    0.1558,
                    60.55018100000001
                ],
                [
                    0.1564,
                    68.381696
                ],
                [
                    0.1567,
                    69.46518400000001
                ],
                [
                    0.1569,
                    69.621464
                ],
                [
                    0.157,
                    70.398015
                ],
                [
                    0.1574,
                    70.400197
                ],
                [
                    0.1575,
                    73.199217
                ],
                [
                    0.158,
                    77.700017
                ],
                [
                    0.1583,
                    79.449017
                ],
                [
                    0.1588,
                    79.584064
                ],
                [
                    0.159,
                    80.584064
                ],
                [
                    0.16,
                    81.58156
                ],
                [
                    0.1608,
                    83.38156
                ]
            ],
            color: '#03a7a8'
        }, {
            name: 'Hourly Rainfall',
            data: [
                [
                    0.1435,
                    242.521842
                ],
                [
                    0.1436,
                    206.49862099999999
                ],
                [
                    0.1437,
                    205.823735
                ],
                [
                    0.1438,
                    197.33275
                ],
                [
                    0.1439,
                    153.677454
                ],
                [
                    0.144,
                    146.007722
                ],
                [
                    0.1442,
                    82.55212900000001
                ],
                [
                    0.1443,
                    59.152814000000006
                ],
                [
                    0.1444,
                    57.942260000000005
                ],
                [
                    0.1445,
                    57.483850000000004
                ],
                [
                    0.1446,
                    52.39210800000001
                ],
                [
                    0.1447,
                    51.867208000000005
                ],
                [
                    0.1448,
                    44.104697
                ],
                [
                    0.1449,
                    40.131217
                ],
                [
                    0.145,
                    31.878217
                ],
                [
                    0.1451,
                    22.794916999999998
                ],
                [
                    0.1453,
                    12.345828999999998
                ],
                [
                    0.1454,
                    10.035642
                ],
                [
                    0.148,
                    9.326642
                ],
                [
                    0.1522,
                    3.76317
                ]
            ],
            color: '#fc5857'
        }]
    });
</script>


@endpush