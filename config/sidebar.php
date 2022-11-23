<?php

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
      'icon' => 'fa fa-table',
      'title' => 'Monitoring',
      'label' => '',
      'url' => '/dashboard/monitoring',
      'route-name' => 'dashboard.monitoring'
    ],
    [
      'icon' => 'fa fa-map-pin',
      'title' => 'Station List',
      'label' => '',
      'url' => '/station',
      'route-name' => 'station.index'
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
      'title' => 'Wire Viration',
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

  ]
];
