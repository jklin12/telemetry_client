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
                    <div class=" ">
                        <div id='map' style="height: 400px;"></div>
                    </div>
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

                        <div class="table-responsive table-striped">
                            <table id="table-data" class="dataTable display compact">
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
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        @foreach($value as $kf => $vf )
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>
                                    @empty
                                    <div class="col-md-4">
                                        <div class="alert alert-warning fade show m-b-10">
                                            <span class="close" data-dismiss="alert">×</span>
                                            Maaf! Data tidak ditemua.
                                        </div>
                                    </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">

                        <div class="table-responsive table-striped">
                            <table id="table-data" class="dataTable display compact">
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
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        @foreach($value as $kf => $vf )
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>
                                    @empty
                                    <div class="col-md-4">
                                        <div class="alert alert-warning fade show m-b-10">
                                            <span class="close" data-dismiss="alert">×</span>
                                            Maaf! Data tidak ditemua.
                                        </div>
                                    </div>
                                    @endforelse
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

                        <div class="table-responsive table-striped">
                            <table id="table-data" class="dataTable display compact">
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
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        @foreach($value as $kf => $vf )
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>
                                    @empty
                                    <div class="col-md-4">
                                        <div class="alert alert-warning fade show m-b-10">
                                            <span class="close" data-dismiss="alert">×</span>
                                            Maaf! Data tidak ditemua.
                                        </div>
                                    </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-body">

                        <div class="table-responsive table-striped">
                            <table id="table-data" class="dataTable display compact">
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
                                        <td class="text-center">{{ $loop->iteration  }}</td>
                                        @foreach($value as $kf => $vf )
                                        <td class="text-center">{{$vf}}</td>
                                        @endforeach
                                    </tr>
                                    @empty
                                    <div class="col-md-4">
                                        <div class="alert alert-warning fade show m-b-10">
                                            <span class="close" data-dismiss="alert">×</span>
                                            Maaf! Data tidak ditemua.
                                        </div>
                                    </div>
                                    @endforelse
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
<script src="/vendor/datatables/buttons.server-side.js"></script>
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
        zoom: 10
    });

    map.on('load', () => {
        map.resize();
        map.loadImage(
            'https://docs.mapbox.com/mapbox-gl-js/assets/custom_marker.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('custom-marker', image, {

                });
                // Add a GeoJSON source with 2 points
                map.addSource('places', {
                    // This GeoJSON contains features that include an "icon"
                    // property. The value of the "icon" property corresponds
                    // to an image in the Mapbox Streets style's sprite.
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
                        'icon-size': 1.5,
                        'icon-allow-overlap': true
                    },

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

            })
    });
</script>

<script>
    Highcharts.getJSON(
        'https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json',
        function(data) {

            Highcharts.chart('container', {
                chart: {
                    zoomType: 'x',
                    height: '50%',


                },
                title: {
                    text: 'Hytograph',
                   
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
    Highcharts.getJSON(
        'https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json',
        function(data) {

            Highcharts.chart('container2', {
                chart: {
                    zoomType: 'x',
                    height: '50%',

                },
                title: {
                    text: 'Judmentgraph',
                },
                subtitle: {
                    text: '13 Dec 2022',
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
    Highcharts.getJSON(
        'https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json',
        function(data) {

            Highcharts.chart('container3', {
                chart: {
                    zoomType: 'x',
                    height: '50%',

                },
                title: {
                    text: 'HydroGraph',
                },
                subtitle: {
                    text: '13 Dec 2022',
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

@endpush