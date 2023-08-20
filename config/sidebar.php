<?php
 
$users = [];

return [


  /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */


  'menu' => [
    [
      'icon' => 'fa fa-th-large',
      'title' => 'Dashboard',
      'label' => '',
      'url' => '/dashboard',
      'route-name' => 'dashboard.index'
    ],
    [
      'icon' => 'fa fa-torii-gate',
      'title' => 'Portal Data',
      'label' => '',
      'url' => '/dashboard/portal',
      'route-name' => 'dashboard.portal'
    ],
    [
      'icon' => 'fa fa-map-pin',
      'title' => 'Station List',
      'label' => '',
      'url' => '/station',
      'route-name' => 'station.index'
    ],
    [
      'icon' => 'fas fa-chart-line',
      'title' => 'Grafik',
      'url' => 'javascript:;',
      'caret' => true,
      'sub_menu' => [
        [
          'title' => 'Judment Graph',
          //'label' => 'NEW',
          'url' => '/grafik/judment',
          'route-name' => 'grafik.judment'
        ],
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
   
  ]
];
