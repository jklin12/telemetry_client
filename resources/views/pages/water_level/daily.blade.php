@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />

@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->

<div class="panel panel-inverse">
    <div class="panel-body">
        <form action="" method="get" id="filter-form">
            <div class="d-sm-flex align-items-center mb-3">
                <a href="#" class="btn btn-inverse btn-pink mr-2 text-truncate" id="datepicker">
                    <i class="fa fa-calendar fa-fw text-white-transparent-5 ml-n1"></i>
                    <span>{{ $filterDate }}</span>
                    <b class="caret"></b>
                    <input id="reservationDate" type="hidden" name="date" />
                </a>

                <!--<div class="text-muted f-w-600 mt-2 mt-sm-0">compared to <span id="daterange-prev-date">24 Mar-30 Apr 2020</span></div>-->
            </div>
        </form>
        <div class="table-responsive table-striped">
            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                @if($datas)
                <table id="table-onu" class="table dataTable no-footer">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Date Time</th>
                            @foreach($datas['station'] as $key => $value)
                            <th class="text-center">{{$value['station_name']}}</th>
                            @endforeach
                        </tr>

                    </thead>
                    <tbody>
                        @forelse($datas['data'] as $key => $value)
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

                        <tr class="table-borderless">
                            <td colspan="{{ count($datas['station'])+ 2}}"></td>
                        </tr>
                        <tr>
                            <td colspan="2">Average</td>
                            @foreach($summaryData['average'] as $key => $value)
                            <td class="text-center">{{$value}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td colspan="2">MAX</td>
                            @foreach($summaryData['max'] as $key => $value)
                            <td class="text-center">{{$value}}</td>
                            @endforeach
                        </tr>

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