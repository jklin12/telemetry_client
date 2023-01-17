@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')

@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header"><i class='fas fa-mountain' style='color: #313e8e'></i> &nbsp;Mt. Merapi Telemetry System</h1>
<!-- end page-header -->

<div class="row">
    <!-- end col-4 -->
    <div class="col-lg-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel">
            <a href="{{ route('grafik.hydrograph') }}" class="widget-card widget-card-rounded m-b-20">
                <div class="panel-body">
                    <h4><i class="fa fa-chart-line fa-fw" style='color: #313e8e'></i> Grafik</h4>
                    <p class="text-inverse">
                        Grafik Hydrograph & Hytrograph
                    </p>
                </div>
            </a>
        </div>
        <!-- end panel -->
    </div>
    <!-- begin col-4 -->
    <!-- end col-4 -->
    <div class="col-lg-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel">
            <a href="{{ route('rainfall.current') }}" class="widget-card widget-card-rounded m-b-20">
                <div class="panel-body">
                    <h4><i class="fa fa-cloud-rain fa-fw" style='color: #313e8e'></i> Rainfall</h4>
                    <p class="text-inverse">
                        Rainfall adalah alat pengukur curah hujan digital yang menggunakan teknologi sensor dan mikroprosessor untuk proses pengukuran tingkat curah hujan
                    </p>

                </div>
            </a>
        </div>
        <!-- end panel -->
    </div>
    <!-- begin col-4 -->
    <!-- end col-4 -->
    <div class="col-lg-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel" data-intro="Intro.js has many examples. Browse this section to find out more." data-step="4">
            <a href="{{ route('water_level.daily') }}" class="widget-card widget-card-rounded m-b-20">
                <div class="panel-body">
                    <h4><i class="fa fa-water fa-fw" style='color: #313e8e'></i> Water Level</h4>
                    <p class="text-inverse">
                        Water level merupakan sensor yang berfungsi untuk mendeteksi ketinggian air dengan output analog kemudian diolah menggunakan mikrokontroler.
                    </p>

                </div>
            </a>
        </div>
        <!-- end panel -->
    </div>
    <!-- begin col-4 -->
    <!-- end col-4 -->
    <div class="col-lg-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel">
            <a href="{{ route('flow.daily') }}" class="widget-card widget-card-rounded m-b-20">
                <div class="panel-body">
                    <h4><i class="fa fa-map-signs fa-fw" style='color: #313e8e'></i> Mudflow</h4>
                    <p class="text-inverse">
                        Mudflow merupakan sensor yang berfungsi untuk mendeteksi pergerakan aliran lumpur  dengan output analog kemudian diolah menggunakan mikrokontroler.
                    </p>

                </div>
            </a>
        </div>
        <!-- end panel -->
    </div>
    <div class="col-lg-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel">
            <a href="{{ route('wire_vibration.daily') }}" class="widget-card widget-card-rounded m-b-20">
                <div class="panel-body">
                    <h4><i class="fa fa-wifi fa-fw" style='color: #313e8e'></i> Wire & Vibration</h4>
                    <p class="text-inverse">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat
                    </p>

                </div>
            </a>
        </div>
        <!-- end panel -->
    </div>
    <!-- end col-4 -->
</div>@endsection



@push('scripts')

@endpush