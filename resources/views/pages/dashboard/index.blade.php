@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
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
<style>
    .map-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #fff;
        margin-right: 50px;
        font-family: Arial, sans-serif;
        overflow: auto;
        border-radius: 3px;
    }

    #legend {
        padding: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        line-height: 18px;
        height: 250px;
        margin-bottom: 40px;
        width: 200px;
    }

    .legend-key {
        display: inline-block;
        border-radius: 20%;
        width: 20px;
        height: 20px;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .legend-key-color {
        display: inline-block;
        border-radius: 20%;
        width: 10px;
        height: 10px;
        margin-right: 5px; 
    }
</style>
@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->


<div class="panel panel-inverse">
    <div class="panel-body">
        <div id="menu" class="mb-2">
            <div class="radio radio-css radio-inline">
                <input type="radio" name="rtoggle" id="satellite-streets-v11" value="satellite" checked="checked">
                <label for="satellite-streets-v11">Satellite Streets</label>
            </div>
            <div class="radio radio-css radio-inline">
                <input type="radio" name="rtoggle" id="streets-v11" value="streets">
                <label for="streets-v11">Streets</label>
            </div>
            <div class="radio radio-css radio-inline">
                <input type="radio" name="rtoggle" id="outdoors-v11" value="outdoors">
                <label for="outdoors-v11">Outdors</label>
            </div>
        </div>
        <div id='map' style='height: 500px;'></div>
        <div class='map-overlay' id='legend'>
            <div><img src="/assets/icons/circle.png" class="legend-key" alt=""><span>Rainfall</span></div>
            <div><img src="/assets/icons/square.png" class="legend-key" alt=""><span>Water Level</span></div>
            <div><img src="/assets/icons/circle.png" class="legend-key" alt=""><span>Mudflow</span></div>
            <div><img src="/assets/icons/square_circle.png" class="legend-key" alt=""><span>Rainfall & Water Level</span></div>
            <div><img src="/assets/icons/square_star.png" class="legend-key" alt=""><span>Water Level & Mudflow</span></div>
            <div><img src="/assets/icons/circle_star.png" class="legend-key" alt=""><span>Rainfall & Mudflow</span></div>
            <hr>
            <div><span class="legend-key-color" style="background-color: #f20953;"></span><span>Inactive Station</span></div>
        </div>


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
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [110.4025134, -7.6269335],
        zoom: 10
    });

    const layerList = document.getElementById('menu');
    const inputs = layerList.getElementsByTagName('input');

    for (const input of inputs) {
        input.onclick = (layer) => {
            const layerId = layer.target.id;
            map.setStyle('mapbox://styles/mapbox/' + layerId);
        };
    }
    map.on('style.load', () => {
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

    /*setInterval(function() {
        $.ajax({
            url: "<?php //echo route('dashboard.alertData') 
                    ?>",
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
    }, 15000);*/
</script>
@endpush