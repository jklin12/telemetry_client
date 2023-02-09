@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<style>
    .hiddenRow {
        padding: 0 !important;
    }
</style>
@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->
@include('includes.component.erorr-message')
@include('includes.component.success-message')

<div class="panel panel-inverse">
    <div class="panel-body">
        <form action="" method="get">
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group ">
                        <input type="text" name="q" class="form-control" placeholder="Cari...">

                    </div>
                </div>
                <div class="col-sm">
                    <button type="submit" class="btn btn-search m-r-3"><i class="fa fa-search"></i></button>
                    <a href="{{ route('station.add')}}" class="btn btn-search btn-indigo"><i class="fa fa-plus"></i>&nbsp;Tambah Data</a>

                </div>
        </form>
        <div class="table-responsive table-striped">
            <table class="table" id="station-table">
                <thead>
                    <th class="text-center">No.</th>
                    @foreach($arrfield as $kf=> $vf)
                    <th class="text-center">{{$vf['label']}}</th>
                    @endforeach
                    <th class="text-center">Action</th>
                </thead>
                <tbody>
                    @foreach($datas as $key=> $value)
                    <!--<tr data-toggle="collapse" data-target="#row-{{$value['station_id'] }}" class="accordion-toggle">-->
                    <tr>
                        <td>{{ $loop->iteration  }}</td>
                        @foreach($arrfield as $kf=> $vf)
                        <td>{{$value[$kf]}}</td>
                        @endforeach
                        <td>
                            <!--<a href="{{ route('station.addType',$value['station_id']) }}" class="btn btn-red btn-icon btn-circle btn-sm"><i class="fas fa-plus"></i></a>
                            @if(isset($value['station_types']))
                            <a href="javascript:;" class="btn btn-success btn-icon btn-circle btn-sm"><i class="fas fa-eye"></i></a>
                            @endif-->
                            <a href="{{ route('station.show', $value['station_id']) }}" class="btn btn-success btn-icon btn-circle btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('station.form', $value['station_id']) }}" class="btn btn-indigo btn-icon btn-circle btn-sm"><i class="fa fa-edit"></i></a>
                            <a href="{{ route('station.form', $value['station_id']) }}" class="btn btn-danger btn-icon btn-circle btn-sm"><i class="fa fa-trash"></i></a>
                        </td>

                    </tr>
                    <!--@if(isset($value['station_types']))
                    <tr class="hiddenRow">
                        <td colspan="12" class="hiddenRow">
                            <div class="accordian-body collapse" id="row-{{$value['station_id'] }}">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="info">
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Equipment</th>
                                             <th class="text-center">Alert Value</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($value['station_types'] as $kTypes => $vTypes )
                                        <tr>
                                            <td>{{ $loop->iteration  }}</td>
                                            <td>{{$vTypes['station_type']}}</td>
                                             <td>{{$vTypes['alert_value']}}</td>
                                            <td class="text-center">
                                                <a href="{{ route('station.formType', [$value['station_id'], $vTypes['id']])  }}" class="btn btn-indigo btn-icon btn-sm btn-circle"><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('station.formType', [$value['station_id'], $vTypes['id']])  }}" class="btn btn-red btn-icon btn-sm btn-circle"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </td>
                    </tr>
                    @endif-->


                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection



@push('scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>

<script>
    var table = $('#station-table').DataTable({
        "paging": false,
        "info": false,
        "search": false,
        "dom": 'lrtip'

    })
</script>

@endpush