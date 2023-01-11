@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.css" rel="stylesheet">
<style>
    .danger-popup .mapboxgl-popup-content {
        background-color: red;
    }

    .danger-popup .mapboxgl-popup-tip {
        border-top-color: red;
    }

    /* change background and tip color to yellow */
    .warning-popup .mapboxgl-popup-content {
        background-color: yellow;
    }

    .warning-popup .mapboxgl-popup-tip {
        border-top-color: yellow;
    }
</style>
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
                                    Maaf! Data belum tersedia.
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
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [110.4025134, -7.6269335],
        zoom: 9
    });

    map.on('load', () => {
        map.loadImage(
            '/assets/icons/building.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('building', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/communications-tower.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('communications-tower', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/square.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('square', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/circle.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('circle', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/star.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('star', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/square_circle.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('square_circle', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/square_star.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('square_star', image, {
                    'sdf': true
                });
            }
        );
        map.loadImage(
            '/assets/icons/circle_star.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('circle_star', image, {
                    'sdf': true
                });
            }
        );

        map.addSource('places', {
            'type': 'geojson',
            'data': {
                'type': 'FeatureCollection',
                'features': <?php echo $station ?>
            }
        });
        // Add a layer showing the places.
        map.addLayer({
            'id': 'places',
            'type': 'symbol',
            'source': 'places',
            'layout': {
                'icon-image': '{icon}',
                'icon-size': 1,
                'icon-allow-overlap': true
            },
            "paint": {
                "icon-halo-color": [
                    'match', // Use the 'match' expression: https://docs.mapbox.com/mapbox-gl-js/style-spec/#expressions-match
                    ['get', 'icon'], // Use the result 'STORE_TYPE' property
                    'circle',
                    '#ffffff',
                    'building',
                    '#ecfc0c',
                    'triangle-stroked',
                    '#182bf7',
                    'communications-tower',
                    '#000000',
                    '#f20953' // any other store type
                ],
                "icon-halo-width": 2,
                'icon-color': [
                    'match', // Use the 'match' expression: https://docs.mapbox.com/mapbox-gl-js/style-spec/#expressions-match
                    ['get', 'icon'], // Use the result 'STORE_TYPE' property
                    'circle',
                    '#ffffff',
                    'building',
                    '#ecfc0c',
                    'triangle-stroked',
                    '#182bf7',
                    'communications-tower',
                    '#000000',
                    '#f20953' // any other store type
                ],
            }

        });

        // When a click event occurs on a feature in the places layer, open a popup at the
        // location of the feature, with description HTML from its properties.
        map.on('click', 'places', (e) => {
            // Copy coordinates array.
            const coordinates = e.features[0].geometry.coordinates.slice();
            const description = e.features[0].properties.description;

            // Ensure that if the map is zoomed out such that multiple
            // copies of the feature are visible, the popup appears
            // over the copy being pointed to.
            while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
            }

            new mapboxgl.Popup()
                .setLngLat(coordinates)
                .setHTML(description)
                .addTo(map);
        });

        // Change the cursor to a pointer when the mouse is over the places layer.
        map.on('mouseenter', 'places', () => {
            map.getCanvas().style.cursor = 'pointer';
        });

        // Change it back to a pointer when it leaves.
        map.on('mouseleave', 'places', () => {
            map.getCanvas().style.cursor = '';
        });
    });


    setInterval(function() {
        $.ajax({
            url: "<?php echo route('dashboard.alertData') ?>",
            success: function(data) {
                var json = JSON.parse(data)
                var time = 2000;
                $.each(json, function(index, value) {

                    const coordinates = value.coordinates.slice();
                    const description = value.element;

                    setTimeout(function() {
                        new mapboxgl.Popup({
                                className: value.class
                            })
                            .setLngLat(coordinates)
                            .setHTML(description)
                            .addTo(map);
                    }, time);
                    time + 1000;
                })

            }
        }).done(function() {
            setTimeout(function() {
                $('.mapboxgl-popup').remove();
            }, 10000);
        })
    }, 15000);
</script>

<script>
    Highcharts.chart('container', {
        chart: {
            type: 'line',
            //height: '50%',
        },
        title: {
            text: '<?php echo $rainfallChart['title_rh'] ?>',
        },
        subtitle: {
            text: '<?php echo $rainfallChart['sub_title'] ?>',
        },
        xAxis: [{
            categories: <?php echo json_encode($rainfallChart['label']) ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: 'Rh(mm/h)'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        series: <?php echo json_encode($rainfallChart['series_rh']) ?>,
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
    Highcharts.chart('container2', {
        chart: {
            type: 'line',
            //height: '50%',
        },
        title: {
            text: '<?php echo $rainfallChart['title_rc'] ?>',
        },
        subtitle: {
            text: '<?php echo $rainfallChart['sub_title'] ?>',
        },
        xAxis: [{
            categories: <?php echo json_encode($rainfallChart['label']) ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: 'Rh(mm/h)'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        series: <?php echo json_encode($rainfallChart['series_rc']) ?>,
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
    Highcharts.chart('container4', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo $flowChart['title'] ?>',
        },
        subtitle: {
            text: '<?php echo $flowChart['sub_title'] ?>',
        },
        xAxis: [{
            categories: <?php echo json_encode($flowChart['label']) ?>,
            crosshair: true
        }],
        yAxis: {
            min: 0,
            title: {
                text: 'Water Level'
            }
        },

        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: <?php echo json_encode($flowChart['series']) ?>
    });
    Highcharts.chart('container3', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo $waterLevelChart['title'] ?>',
        },
        subtitle: {
            text: '<?php echo $waterLevelChart['sub_title'] ?>',
        },
        xAxis: [{
            categories: <?php echo json_encode($waterLevelChart['label']) ?>,
            crosshair: true
        }],
        yAxis: {
            min: 0,
            title: {
                text: 'Water Level'
            }
        },

        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: <?php echo json_encode($waterLevelChart['series']) ?>
    });
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