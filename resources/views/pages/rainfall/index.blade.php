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
        <form action="" method="get" id="filter-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">Station :</label>
                        <div class="col-md-9">
                            <select class="default-select2 form-control border border-danger" id="station" name="station" required>
                                <option value="">-- Pilih Profile --</option>
                                @forelse($station_list as $key => $value)
                                <option value="{{$value['station_id']}}">{{$value['station_name']}}</option>
                                @empty
                                <option value="">Data tidak ditemukan</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">Date : </label>
                        <div class="col-md-9">
                            <a href="#" class="btn  btn-indigo mr-2 text-truncate" id="datepicker">
                                <i class="fa fa-calendar fa-fw text-white-transparent-5 ml-n1"></i>
                                <span>{{ $filterDate }}</span>
                                <b class="caret"></b>
                                <input id="reservationDate" type="hidden" name="date" value="{{ $filterDate }}"/>
                            </a>
                        </div>
                    </div>
                </div>


                <!--<div class="text-muted f-w-600 mt-2 mt-sm-0">compared to <span id="daterange-prev-date">24 Mar-30 Apr 2020</span></div>-->
            </div>
        </form>
        <div class="table-responsive table-striped">
            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                @if($datas)
                <table id="table-data" class="table dataTable no-footer">
                    <thead>
                        <tr>
                            <th>No.</th>
                            @foreach($arr_field as $key => $value)
                            <th class="text-center">{{$value['label']}}</th>
                            @endforeach
                        </tr>

                    </thead>
                    <tbody>
                        @forelse($datas as $key => $value)
                        <tr>
                            <td>{{ $loop->iteration  }}</td>
                            @foreach($arr_field as $kf => $vf )
                            <td>{{$value[$kf]}}</td>
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
    $(".default-select2").select2().on('select2:select', function(e) {
        var data = e.params.data;
        $('#filter-form').submit();
    }).select2('val', '<?php echo $filterStation?>');
    $("#datepicker").datepicker({
        format: 'yyyy-mm-dd',
        defaultDate: '<?php echo $filterDate ?>'
    }).on('changeDate', function(ev) {

        $('#filter-form').submit();
    });
</script>


@endpush