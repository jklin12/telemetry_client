@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
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

        <form action="{{ route('mapjson.update',$data['id'])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @foreach($arrField as $kf => $vf)
            <div class="form-group row m-b-15">
                <label class="col-form-label col-md-3">{{ $vf['label'] }}</label>
                <div class="col-md-9">
                    @if($vf['form_type'] == 'text')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ isset($data[$kf]) ? $data[$kf] : '' }}">
                    @elseif($vf['form_type'] == 'password')
                    <input type="password" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="************">
                    @elseif($vf['form_type'] == 'color')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ isset($data[$kf]) ? $data[$kf] : '' }}">
                    @elseif($vf['form_type'] == 'date')
                    <input type="text" name="{{ $kf }}" id="input_{{ $kf }}" class="form-control m-b-5 datetimepicker_input" placeholder="Masukan {{ $vf['label'] }}" />
                    @elseif($vf['form_type'] == 'area')
                    <textarea class="form-control" name="{{ $kf }}" id="input_{{ $kf }}" rows="3"></textarea>
                    @elseif($vf['form_type'] == 'select')
                    <select class="form-control m-b-5" name="{{ $kf }}" id="input_{{ $kf }}" autocomplete="off">
                        <option value="">Pilih {{ $vf['label'] }}</option>
                        @foreach($vf['keyvaldata'] as $kvdata => $vdata)
                        <option value="{{ $kvdata }}" {{ $kvdata == $vf['valdata'] ? 'selected' : '' }}>{{$vdata}}</option>
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

@endpush