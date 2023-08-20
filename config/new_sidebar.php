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
        $absensi = [];
        $home = [
            'icon' => 'fas fa-home',
            'title' => 'Home',
            'label' => '',
            'url' => '/',
            'route-name' => 'home.index'
        ];
        $yearReport = [];
        $monthReport = [];
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
            $absensi = [
                'icon' => 'fa fa-file',
                'title' => 'Data Absensi',
                'label' => '',
                'url' => '/absen',
                'route-name' => 'absensi.index'
            ];
            $stationList = [
                'icon' => 'fa fa-building',
                'title' => 'Station',
                'url' => 'javascript:;',
                'caret' => true,
                'sub_menu' => [
                    [
                        'title' => 'Station List',
                        'url' => '/station',
                        'route-name' => 'station.index'
                    ],
                    [
                        'title' => 'Station Assets',
                        //'label' => 'NEW',
                        'url' => '/station_assets',
                        'route-name' => 'station_assets.index'
                    ],
                    [
                        'title' => 'Station Maintenance',
                        //'label' => 'NEW',
                        'url' => '/station_history',
                        'route-name' => 'station_history.index'
                    ]
                ]
            ];
            $monthReport =   [
                'title' => 'Rainfall Monthly Report',
                //'label' => 'NEW',
                'url' => '/rainfall/monthly',
                'route-name' => 'rainfall.monthly'
            ];
            $yearReport =  [
                'title' => 'Rainfall Yearly Report',
                //'label' => 'NEW',
                'url' => '/rainfall/yearly',
                'route-name' => 'rainfall.yearly'
            ];

            $home = [];
        }

        $menu = [
            $home,
            $dashboard,
            $portal,
            $stationList,
            /*[
                'icon' => 'fas fa-chart-line',
                'title' => 'Chart',
                'url' => 'javascript:;',
                'caret' => true,
                'sub_menu' => [
                    
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
                'title' => 'Rainfall',
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
                    $monthReport,
                    $yearReport

                ]
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
            ],*/
            [
                'icon' => 'fas fa-water',
                'title' => 'Water Level',
                'label' => '',
                'url' => '/water_level/daily',
                'route-name' => 'water_level.daily'
            ],
            $absensi,
            $csvDownload,
            $geojson,
            $users,
        ];
        return $menu;
    }
}
