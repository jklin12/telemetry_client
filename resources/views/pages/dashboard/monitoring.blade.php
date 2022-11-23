@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small></small></h1>
<!-- end page-header -->


<div class="row">
    <div class="col-xl-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel panel-inverse" data-sortable-id="ui-buttons-1">
            <!-- begin panel-heading -->
            <div class="panel-heading ui-sortable-handle" style="background: #37474f;">
                <h4 class="panel-title">{{$curentRainFall['title']}} </h4>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="table-responsive table-striped">
                    <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                        @if($curentRainFall['data'])
                        <table id="table-data" class="table dataTable no-footer">
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
                                    @foreach($value as $kf => $vf )
                                    <td>{{$vf}}</td>
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
            <!-- end panel-body -->

        </div>
        <!-- end panel -->
    </div>
    <div class="col-xl-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel panel-inverse" data-sortable-id="ui-buttons-1">
            <!-- begin panel-heading -->
            <div class="panel-heading ui-sortable-handle" style="background: #37474f;">
                <h4 class="panel-title">{{$waterLevel['title']}} </h4>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="table-responsive table-striped">
                    <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                        @if($waterLevel['data'])
                        <table id="table-onu" class="table dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date Time</th>
                                    @foreach($waterLevel['data']['station'] as $key => $value)
                                    <th class="text-center">{{$value['station_name']}}</th>
                                    @endforeach
                                </tr>

                            </thead>
                            <tbody>
                                @forelse($waterLevel['data']['data'] as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration  }}</td>
                                    <td>{{ $value['date_time']}}</td>
                                    @foreach($value['datas'] as $kdata => $vdata)
                                    <td>{{ $vdata['water_level_hight']}}</td>
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
            <!-- end panel-body -->

        </div>
        <!-- end panel -->
    </div>
    <div class="col-xl-4 ui-sortable">
        <!-- begin panel -->
        <div class="panel panel-inverse" data-sortable-id="ui-buttons-1">
            <!-- begin panel-heading -->
            <div class="panel-heading ui-sortable-handle" style="background: #37474f;">
                <h4 class="panel-title">{{$wireVibration['title']}} </h4>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="table-responsive table-striped">
                    <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                        @if($wireVibration['data'])
                        <table id="table-onu" class="table dataTable no-footer">
                            <thead>
                                <tr>
                                    <th rowspan="2">No.</th>
                                    <th rowspan="2">Date Time</th>
                                    @foreach($wireVibration['data']['station'] as $key => $value)
                                    <th colspan="2" class="text-center">{{$value['station_name']}}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($wireVibration['data']['station'] as $key => $value)
                                    <th>Wire</th>
                                    <th>Vibration</th>
                                    @endforeach

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wireVibration['data']['data'] as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration  }}</td>
                                    <td>{{ $value['date_time']}}</td>
                                    @foreach($value['datas'] as $kdata => $vdata)
                                    <td>{{ $vdata['wire']}}</td>
                                    <td>{{ $vdata['vibration']}}</td>
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
                <!-- end panel-body -->

            </div>
            <!-- end panel -->
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

    <script>
        ///alert(Date())

        $("#datepicker").datepicker({
            format: 'yyyy-mm-dd',
            defaultDate: '<?php echo $filterDate ?>'
        }).on('changeDate', function(ev) {

            $('#filter-form').submit();
        });
    </script>


    @endpush