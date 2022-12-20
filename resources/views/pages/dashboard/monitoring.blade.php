@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.css" rel="stylesheet">
@endpush

@section('content')
<div style="height: 600px; ">
    <div class=" d-inline-block w-100 h-50" style=" ">
        <div class="h-50">
            <div class="row">
                <div class="col ">
                    <div id='map' style="height:400px;"></div>

                </div>

                <div class="col ">
                    <div class="bd-example">
                        <div class="row">
                            <div class="col m-r-3" style="height:150px">
                                <div id="container"></div>
                            </div>
                            <div class="col m-r-3">
                                <div id="container2"></div>
                            </div>

                            <!-- Force next columns to break to new line at md breakpoint and up -->
                            <div class="w-100 d-none d-md-block"></div>

                            <div class="col m-r-3">
                                <div id="container3"></div>
                            </div>
                            <div class="col m-r-3">
                                <div id="container4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class=" d-inline-block w-100 h-50" style=" ">
        <div class="row m-b-3">
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">
                        <h1 class="page-header" style="margin: 0px;"><small>{{$curentRainFall['title']}}</small></h1>
                        <div class="table-responsive">
                            @if($curentRainFall['data'])
                            <table id="table-rainfall" class="table table-striped table-bordered display compact">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th class="text-center">Station</th>
                                        <th class="text-center">10-min Rainfall</th>
                                        <th class="text-center">30-min Rainfall</th>
                                        <th class="text-center">Hourly Rainfall</th>
                                        <th class="text-center">3-hr Rainfall</th>
                                        <th class="text-center">6-hr Rainfall</th>
                                        <th class="text-center">12-hr Rainfall</th>
                                        <th class="text-center">24-hr Rainfall</th>
                                        <th class="text-center">Continous Rainfall</th>
                                        <th class="text-center">Effective Rainfall</th>
                                        <th class="text-center">Effective Intensity</th>
                                        <th class="text-center">Previous Working</th>
                                        <th class="text-center">Working Rainfal</th>
                                        <th class="text-center">Working Rainfall (half-life:24h)</th>
                                        <th class="text-center">Remarks</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @forelse($curentRainFall['data'] as $key => $value)
                                    <tr>
                                        <td>{{ $loop->iteration  }}</td>
                                        <td>{{$value->station}}</td>
                                        <td>{{$value->rain_fall_10_minut}}</td>
                                        <td>{{$value->rain_fall_30_minute}}</td>
                                        <td>{{$value->rain_fall_1_hour}}</td>
                                        <td>{{$value->rain_fall_3_hour}}</td>
                                        <td>{{$value->rain_fall_6_hour}}</td>
                                        <td>{{$value->rain_fall_12_hour}}</td>
                                        <td>{{$value->rain_fall_24_hour}}</td>
                                        <td>{{$value->rain_fall_continuous}}</td>
                                        <td>{{$value->rain_fall_effective}}</td>
                                        <td>{{$value->rain_fall_effective_intensity}}</td>
                                        <td>{{$value->rain_fall_prev_working}}</td>
                                        <td>{{$value->rain_fall_working}}</td>
                                        <td>{{$value->rain_fall_working_24}}</td>
                                        <td>{{$value->rain_fall_remarks}}</td>
                                    </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                            @else
                            <div class="">
                                <div class="alert alert-warning fade show m-b-10">
                                    <span class="close" data-dismiss="alert">×</span>
                                    Maaf! Data tidak ditemua.
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">
                        <h1 class="page-header" style="margin: 0px;"><small>{{ $waterLevel['title']}}</small></h1>
                        <div class="table-responsive">
                            <table id="table-waterlevel" class="table table-striped table-bordered table-td-valign-middle">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th class="text-center">Station</th>
                                        @if(isset($waterLevel['data']['station']))
                                        @foreach($waterLevel['data']['station'] as $key => $value)
                                        <th class="text-center">{{$value}}</th>
                                        @endforeach
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @if(isset($waterLevel['data']['data']))
                                    @foreach($waterLevel['data']['data'] as $key => $value)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        <td class="text-center">{{$value['time']}}</td>
                                        @foreach($value['data'] as $kf => $vf)
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach

                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-b-3">
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">
                        <h1 class="page-header" style="margin: 0px;"><small>{{ $wireVibration['title']}}</small></h1>
                        <div class="table-responsive">
                            <table id="table-wirevibration" class="table table-striped table-bordered table-td-valign-middle ">

                                <thead>
                                    @if(isset($wireVibration['data']['station']))
                                    <tr>
                                        <th>No.</th>
                                        <th class="text-center">Date Time</th>
                                        @foreach($wireVibration['data']['station'] as $key => $value)
                                        <th class="text-center" colspan="2">{{$value}}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th class="text-center"></th>
                                        @foreach($wireVibration['data']['station'] as $key => $value)
                                        <th class="text-center">Wire</th>
                                        <th class="text-center">Vibration</th>
                                        @endforeach
                                    </tr>
                                    @endif
                                </thead>

                                <tbody>
                                    @if(isset($wireVibration['data']['data']))
                                    @foreach($wireVibration['data']['data'] as $key => $value)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        <td class="text-center">{{$value['date_time']}}</td>
                                        @foreach($value['datas'] as $kf => $vf)
                                        <td class="text-center">{{$vf['wire']}}</td>
                                        <td class="text-center">{{$vf['vibration']}}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">
                        <h1 class="page-header" style="margin: 0px;"><small>{{ $flow['title']}}</small></h1>
                        <div class="table-responsive">
                            <table id="table-flow" class="table table-striped table-bordered table-td-valign-middle">

                                <thead>
                                    @if(isset($flow['data']['station']))
                                    <tr>
                                        <th>No.</th>
                                        <th class="text-center">Station</th>
                                        @foreach($flow['data']['station'] as $key => $value)
                                        <th class="text-center">{{$value}}</th>
                                        @endforeach
                                    </tr>
                                    @endif

                                </thead>

                                <tbody>
                                    @if(isset($flow['data']['data']))
                                    @foreach($flow['data']['data'] as $key => $value)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        <td class="text-center">{{$value['date_time']}}</td>
                                        @foreach($value['datas'] as $kf => $vf)
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>

                                    @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection



@push('scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>

<script>
    mapboxgl.accessToken = 'pk.eyJ1IjoiZmFyaXNhaXp5IiwiYSI6ImNrd29tdWF3aDA0ZDAycXVzMWp0b2w4cWQifQ.tja8kdSB4_zpO5rOgGyYrQ';
    const map = new mapboxgl.Map({
        container: 'map',
        // Choose from Mapbox's core styles, or make your own style with Mapbox Studio
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [110.4025134, -7.6269335],
        zoom: 9
    });

    map.on('load', () => {
        map.loadImage(
            'https://docs.mapbox.com/mapbox-gl-js/assets/custom_marker.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('custom-marker', image, {

                });

                map.addSource('places', {
                    'type': 'geojson',
                    'data': {
                        'type': 'FeatureCollection',
                        'features': <?php echo $station ?>
                    }
                });

                map.addLayer({
                    'id': 'places',
                    'type': 'symbol',
                    'source': 'places',
                    'layout': {
                        'icon-image': '{icon}',
                        'icon-size': 1.5,
                        'icon-allow-overlap': true
                    },

                });

                map.on('click', 'places', (e) => {
                    const coordinates = e.features[0].geometry.coordinates.slice();
                    const description = e.features[0].properties.description;


                    while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                        coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                    }

                    new mapboxgl.Popup()
                        .setLngLat(coordinates)
                        .setHTML(description)
                        .addTo(map);
                });


                map.on('mouseenter', 'places', () => {
                    map.getCanvas().style.cursor = 'pointer';
                });


                map.on('mouseleave', 'places', () => {
                    map.getCanvas().style.cursor = '';
                });

            })
    });

    
</script>

<script>
    Highcharts.chart('container', {
        chart: {
            type: 'area',
            zoomType: 'xy',
            height: '50%',
        },
        title: {
            text: 'Judmentgraph',
        },
        subtitle: {
            text: '13 Dec 2022',
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

    Highcharts.chart('container2', {
        chart: {
            zoomType: 'xy',
            height: '50%',
        },
        title: {
            text: 'HydroGraph',
        },
        subtitle: {
            text: '13 Dec 2022',
        },
        xAxis: [{
            categories: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
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
                text: 'Water Level',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            opposite: true

        }, { // Secondary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Rainfall',
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
            data: [3, 8, 6, 4, 10, 2, 5],

            tooltip: {
                valueSuffix: ' m/s'
            }

        }, {
            name: 'Water Level',
            type: 'spline',
            yAxis: 2,
            data: [0.10, 0.28, 0.35, 0.28, 0.19, 0.10, 0.5],
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
    Highcharts.chart('container3', {
        chart: {
            zoomType: 'xy',
            height: '50%',
        },
        title: {
            text: 'HytroGraph',
        },
        subtitle: {
            text: '13 Dec 2022',
        },
        xAxis: [{
            categories: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value} m',
                style: {
                    color: Highcharts.getOptions().colors[4]
                }
            },
            title: {
                text: 'Water Level',
                style: {
                    color: Highcharts.getOptions().colors[4]
                }
            },
            opposite: true

        }, { // Secondary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Rainfall',
                style: {
                    color: Highcharts.getOptions().colors[3]
                }
            },
            labels: {
                format: '{value} Rh (mm/h)',
                style: {
                    color: Highcharts.getOptions().colors[3]
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
                format: '{value} Rc (mm/h)',
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
            type: 'column',
            yAxis: 1,
            data: [3, 8, 6, 4, 10, 2, 5],

            tooltip: {
                valueSuffix: ' Rc (mm/h)'
            }

        }, {
            name: 'Water Level',
            type: 'spline',
            yAxis: 2,
            data: [0.10, 0.28, 0.35, 0.28, 0.19, 0.10, 0.5],
            tooltip: {
                valueSuffix: ' Rh (mm/h)'
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
    Highcharts.getJSON(
        'https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json',
        function(data) {

            Highcharts.chart('container4', {
                chart: {
                    zoomType: 'x',
                    height: '50%',

                },
                title: {
                    text: '',
                    style: {
                        display: 'none'
                    }
                },
                subtitle: {
                    text: '',
                    style: {
                        display: 'none'
                    }
                },

                xAxis: {
                    type: 'datetime'
                },
                yAxis: {
                    title: {
                        text: 'Exchange rate',
                        style: {
                            display: 'none'
                        }
                    }
                },

                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },

                series: [{
                    showInLegend: false,
                    type: 'area',
                    name: '',
                    data: data
                }],
            });
        }
    );
</script>

<script>
    var wireVibrationTable = $('#table-wirevibration').DataTable({
        paging: false,
        ordering: false,
        searching: false,
        responsive: true
    });
    var flowTable = $('#table-flow').DataTable({
        paging: false,
        ordering: false,
        searching: false,
        responsive: true
    });
    var waterLevelTable = $('#table-waterlevel').DataTable({
        paging: false,
        ordering: false,
        searching: false,
        responsive: true
    });

    var rainFallTable = $('#table-rainfall').DataTable({
        paging: false,
        ordering: false,
        searching: false,
        responsive: true
    });

    setInterval(function() {
        rainFallTable.clear().draw();
        waterLevelTable.clear().draw();
        wireVibrationTable.clear().draw();
        flowTable.clear().draw();

        $.ajax({
            url: "<?php echo route('dashboard.portal_data') ?>",
            success: function(data) {
                var json = JSON.parse(data)
                //alert(json.curent_rainfall)
                rainFallTable.rows.add(json.curent_rainfall).draw();
                waterLevelTable.rows.add(json.water_level).draw();
                wireVibrationTable.rows.add(json.wire_vibration).draw();
                flowTable.rows.add(json.flow).draw();
            }
        }).done(function() {

        });

    }, 1000 * 300);
    //alert(rainFallTable)
</script>
@endpush