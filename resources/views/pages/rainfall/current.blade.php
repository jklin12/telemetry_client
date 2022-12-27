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
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->

<div class="panel panel-inverse">
    <div class="panel-body">

        <div class="table-responsive table-striped">
            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                @if($datas)
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
                        @forelse($datas as $key => $value)
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
                        <div class="col-md-4">
                            <div class="alert alert-warning fade show m-b-10">
                                <span class="close" data-dismiss="alert">×</span>
                                Maaf! Data belum tersedia.
                            </div>
                        </div>
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