@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
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

        <form action="{{ route('station_assets.update',$data->assets_id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @METHOD('PUT')

            @foreach($arrField as $kf => $vf)
            <div class="form-group row m-b-15">
                <label class="col-form-label col-md-3">{{ $vf['label'] }}</label>
                <div class="col-md-9">
                    @if($vf['form_type'] == 'text')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ $data[$kf] ??  '' }}">
                    @elseif($vf['form_type'] == 'password')
                    <input type="password" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ $data[$kf] ?? '' }}">
                    @elseif($vf['form_type'] == 'year')
                    <div class="input-group date" id="year-picker" >
                        <input type="text" class="form-control" name="{{ $kf }}" id="input_{{ $kf }}"  value="{{ $data[$kf] ??  '' }}"/>
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                    @elseif($vf['form_type'] == 'date')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5 datetimepicker_input" placeholder="Masukan {{ $vf['label'] }}" />
                    @elseif($vf['form_type'] == 'color')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ $data[$kf] ??  '' }}">
                    @elseif($vf['form_type'] == 'area')
                    <textarea class="form-control" name="{{ $kf }}" id="input_{{ $kf }}" rows="3">{{ $data[$kf] ??  '' }}</textarea>
                    @elseif($vf['form_type'] == 'select2')
                    <select class="default-select2 form-control m-b-5" name="{{ $kf }}" id="input_{{ $kf }}">
                        <option value="">Pilih {{ $vf['label'] }}</option>
                        @foreach($vf['keyvaldata'] as $kvdata => $vdata)
                        <option value="{{ $kvdata }}">{{$vdata}}</option>
                        @endforeach
                    </select>
                    @elseif($vf['form_type'] == 'select')
                    <select class="form-control m-b-5" name="{{ $kf }}" id="input_{{ $kf }}">
                        <option value="">Pilih {{ $vf['label'] }}</option>
                        @foreach($vf['keyvaldata'] as $kvdata => $vdata)
                        <option value="{{ $kvdata }}">{{$vdata}}</option>
                        @endforeach
                    </select>
                    @elseif($vf['form_type'] == 'select_bsn')
                    <select class="form-control m-b-5" name="{{ $kf }}" id="input_{{ $kf }}">
                        <option value="">Pilih {{ $vf['label'] }}</option>
                        @foreach($vf['keyvaldata'] as $kvdata => $vdata)
                        <option value="{{ $kvdata }}">{{$vdata['parent']}}</option>
                        @if(isset($vdata['child']))
                        @foreach($vdata['child'] as $kchild => $vchild)
                        <option value="{{ $kchild }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$vchild}}</option>
                        @endforeach
                        @endif
                        @endforeach
                    </select>
                    @elseif($vf['form_type'] == 'file')
                    <input type="file" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}">
                    @endif
                </div>
            </div>
            @endforeach

            <div class="pull-right">
                <button type="submit" class="btn btn-pink">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection



@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script>
    $(".default-select2").select2()
    
    $("#year-picker").datepicker({
        format: "yyyy",
        startView: "years",
        minViewMode: "years",
        minDate: "2020-01-01",
        maxDate: new Date(),
    })

    $('#input_station').val(<?php echo $data->station?>).change()
</script>

@endpush