@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.css" rel="stylesheet">
@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->
<!-- begin row -->
<div class="row">
    <!-- begin col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-teal">
            <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
            <div class="stats-content">
                <div class="stats-title">TODAY'S VISITS</div>
                <div class="stats-number">7,842,900</div>
                <div class="stats-progress progress">
                    <div class="progress-bar" style="width: 70.1%;"></div>
                </div>
                <div class="stats-desc">Better than last week (70.1%)</div>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon stats-icon-lg"><i class="fa fa-dollar-sign fa-fw"></i></div>
            <div class="stats-content">
                <div class="stats-title">TODAY'S PROFIT</div>
                <div class="stats-number">180,200</div>
                <div class="stats-progress progress">
                    <div class="progress-bar" style="width: 40.5%;"></div>
                </div>
                <div class="stats-desc">Better than last week (40.5%)</div>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-indigo">
            <div class="stats-icon stats-icon-lg"><i class="fa fa-archive fa-fw"></i></div>
            <div class="stats-content">
                <div class="stats-title">NEW ORDERS</div>
                <div class="stats-number">38,900</div>
                <div class="stats-progress progress">
                    <div class="progress-bar" style="width: 76.3%;"></div>
                </div>
                <div class="stats-desc">Better than last week (76.3%)</div>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-dark">
            <div class="stats-icon stats-icon-lg"><i class="fa fa-comment-alt fa-fw"></i></div>
            <div class="stats-content">
                <div class="stats-title">NEW COMMENTS</div>
                <div class="stats-number">3,988</div>
                <div class="stats-progress progress">
                    <div class="progress-bar" style="width: 54.9%;"></div>
                </div>
                <div class="stats-desc">Better than last week (54.9%)</div>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
</div>
<!-- end row -->

<div class="panel panel-inverse">
    <div class="panel-body">
        <div id='map' style='height: 500px;'></div>
    </div>
</div>

@endsection



@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.js"></script>
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
                        'features': <?php echo $datas ?>
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
@endpush