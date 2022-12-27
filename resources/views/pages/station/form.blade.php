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

<div class="panel panel-inverse">
    <div class="panel-body">

        <form action="{{ route('station.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="action" value="{{ $action}}">
            @if($action == 'editData')
            <input type="hidden" name="station_id" value="{{ isset($data['station_id']) ? $data['station_id'] : '' }}">
            @endif
            @foreach($arrField as $kf => $vf)
            <div class="form-group row m-b-15">
                <label class="col-form-label col-md-3">{{ $vf['label'] }}</label>
                <div class="col-md-9">
                    @if($vf['form_type'] == 'text')
                    <input type="text" name="data[{{ $kf }}]" id="input_{{ $kf }}" class="form-control m-b-5" placeholder="Masukan {{ $vf['label'] }}" value="{{ isset($data[$kf]) ? $data[$kf] : '' }}">
                    @elseif($vf['form_type'] == 'date')
                    <input type="text" name="data[{{ $kf }}]" id="input_{{ $kf }}" class="form-control m-b-5 datetimepicker_input" placeholder="Masukan {{ $vf['label'] }}" />
                    @elseif($vf['form_type'] == 'area')
                    <textarea class="form-control" name="data[{{ $kf }}]" id="input_{{ $kf }}" rows="3"></textarea>
                    @elseif($vf['form_type'] == 'select')
                    <select class="form-control m-b-5" name="data[{{ $kf }}]" id="input_{{ $kf }}">
                        <option value="">Pilih {{ $vf['label'] }}</option>
                        @foreach($vf['keyvaldata'] as $kvdata => $vdata)
                        <option value="{{ $kvdata }}">{{$vdata}}</option>
                        @endforeach
                    </select>
                    @elseif($vf['form_type'] == 'select_bsn')
                    <select class="form-control m-b-5" name="data[{{ $kf }}]" id="input_{{ $kf }}">
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