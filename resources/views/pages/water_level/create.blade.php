@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->
@include('includes.component.erorr-message')
@include('includes.component.success-message')
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="panel panel-inverse">
    <div class="panel-body">

        <form action="{{ route('water_level.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group row m-b-15">
                <label class="col-form-label col-md-3">Station</label>
                <div class="col-md-9">
                    <select class="form-control m-b-5 default-select2" name="station" id="input_station">
                        <option value="">Pilih Station</option>
                        @foreach($stations as $key => $station)
                        <option value="{{ $station->station_id }}">{{$station->station_name}}</option>

                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row m-b-15">
                <label class="col-form-label col-md-3">Tanggal</label>
                <div class="col-md-9">
                    <input type="text" name="water_level_date" id="input_water_level_date" class="form-control m-b-5 datetimepicker_input" placeholder="Masukan Tanggal" />
                </div>
            </div>
            <table id="data-table-fixed-header" class="table table-striped table-bordered table-td-valign-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th class="text-center">Time</th>

                        <th class="text-center">Water Level <br>(m) </th>

                    </tr>

                </thead>
                <tbody>
                    @foreach($times as $key => $time)
                    <tr>
                        <td>{{ $loop->iteration  }}</td>
                        <td>{{ $key  }}</td>
                        <td> <input type="text" name="water_level_hight[{{ $key  }}]" id="input_water_level_hight" class="form-control " placeholder="Masukan Ketinggian Air " value="0"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>



            <div class="pull-right">
                <button type="submit" class="btn btn-pink">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection



@push('scripts')
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script>
    ///alert(Date())

    $(".default-select2").select2();
    $('.datetimepicker_input').datepicker({
        format: 'yyyy-mm-dd',
    });
</script>
@endpush