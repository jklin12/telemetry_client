<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (!function_exists('side_menu')) {
    function side_menu()
    {
        $users = [];
        $geojson = [];
        $csvDownload = [];
        $dashboard = [];
        $portal = [];
        $stationList = [];
        $home = [
            'icon' => 'fas fa-home',
            'title' => 'Home',
            'label' => '',
            'url' => '/',
            'route-name' => 'home.index'
        ];

        if (Auth::user() && Auth::user()->isAdmin) {
            $users = [
                'icon' => 'fa fa-users',
                'title' => 'Users',
                'label' => '',
                'url' => '/users',
                'route-name' => 'users.index'
            ];
            $geojson = [
                'icon' => 'fa fa-layer-group',
                'title' => 'Gejson',
                'label' => '',
                'url' => '/mapjson',
                'route-name' => 'mapjson.index'
            ];
            $csvDownload = [
                'icon' => 'fas fa-download',
                'title' => 'CSV Download',
                'label' => '',
                'url' => '/download/index',
                'route-name' => 'download.index'
            ];
            $dashboard = [
                'icon' => 'fa fa-th-large',
                'title' => 'Dashboard',
                'label' => '',
                'url' => '/dashboard',
                'route-name' => 'dashboard.index'
            ];
            $portal = [
                'icon' => 'fa fa-torii-gate',
                'title' => 'Portal Data',
                'label' => '',
                'url' => '/dashboard/portal',
                'route-name' => 'dashboard.portal'
            ];
            $stationList =  [
                'icon' => 'fa fa-map-pin',
                'title' => 'Station List',
                'label' => '',
                'url' => '/station',
                'route-name' => 'station.index'
            ];
            $home = [];
        }

        $menu = [
            $home,
            $dashboard,
            $portal,    
            $stationList,
            [
                'icon' => 'fas fa-chart-line',
                'title' => 'Grafik',
                'url' => 'javascript:;',
                'caret' => true,
                'sub_menu' => [
                    /*[
                        'title' => 'Judment Graph',
                        //'label' => 'NEW',
                        'url' => '/grafik/judment',
                        'route-name' => 'grafik.judment'
                    ],*/
                    [
                        'title' => 'Hydrograph',
                        //'label' => 'NEW',
                        'url' => '/grafik/hydrograph',
                        'route-name' => 'grafik.hydrograph'
                    ],
                    [
                        'title' => 'Hytrograph',
                        //'label' => 'NEW',
                        'url' => '/grafik/hytrograph',
                        'route-name' => 'grafik.hytrograph'
                    ],
                ]
            ],
            [
                'icon' => 'fas fa-cloud-rain',
                'title' => 'Data Rainfall',
                'url' => 'javascript:;',
                'caret' => true,
                'sub_menu' => [
                    [
                        'title' => 'Curent Rainfall',
                        //'label' => 'NEW',
                        'url' => '/rainfall/current',
                        'route-name' => 'rainfall.current'
                    ],
                    [
                        'title' => 'Rainfall By Station',
                        //'label' => 'NEW',
                        'url' => '/rainfall/byStation',
                        'route-name' => 'rainfall.byStation'
                    ],
                    [
                        'title' => 'Rainfall Daily Report',
                        //'label' => 'NEW',
                        'url' => '/rainfall/daily',
                        //'url' => route('rainfall.daily'),
                        'route-name' => 'rainfall.daily'
                    ],

                ]
            ],
            [
                'icon' => 'fas fa-water',
                'title' => 'Water Level',
                'label' => '',
                'url' => '/water_level/daily',
                'route-name' => 'water_level.daily'
            ],
            [
                'icon' => 'fas fa-map-signs',
                'title' => 'Wire Vibration',
                'label' => '',
                'url' => '/wire_vibration/daily',
                'route-name' => 'wire_vibration.daily'
            ],
            [
                'icon' => 'fas fa-wifi',
                'title' => 'Flow Daily Report',
                'label' => '',
                'url' => '/flow/daily',
                'route-name' => 'flow.daily'
            ],
            $csvDownload,
            $geojson,
            $users,
        ];
        return $menu;
    }
}
