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

        <div class="row">
            <div class="col-xl-6 ui-sortable">
                <h5 class="media-heading "><strong>Station {{ $station['station_name']}} </strong></h5>
                <div class="table-responsive ">
                    <table class="table table-striped m-b-0 no-border">
                        <tbody>
                            <tr>
                                <td><strong>Latitude</strong></td>
                                <td></td>
                                <td> {{ $station['station_lat']}}</td>
                            </tr>

                            <tr>
                                <td><strong>Longitude</strong></td>
                                <td></td>
                                <td> {{ $station['station_long']}}</td>
                            </tr>

                            <tr>
                                <td><strong>River</strong></td>
                                <td></td>
                                <td> {{ $station['station_river']}}</td>
                            </tr>

                            <tr>
                                <td><strong>Product Year</strong></td>
                                <td></td>
                                <td> {{ $station['station_prod_year']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Instalation Date</strong></td>
                                <td></td>
                                <td> {{ $station['station_instalaton_text']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Authority</strong></td>
                                <td></td>
                                <td> {{ $station['station_authority']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Guardsman</strong></td>
                                <td></td>
                                <td> {{ $station['station_guardsman']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Register Number</strong></td>
                                <td></td>
                                <td> {{ $station['station_reg_number']}}</td>
                            </tr>



                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-xl-6 ui-sortable">
                <h5 class="media-heading "><strong>Equipment</strong></h5>
                <div class="table-responsive ">
                    <table class="table table-striped m-b-0 no-border">
                        <thead>
                            <tr>
                                <th><strong>No.</strong></th>
                                <th><strong>Equipment</strong></th>
                                <th><strong>Alert Value</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($station['station_types'] as $key => $value)
                            <tr>
                                <td>{{ $loop->iteration}}</td>
                                <td>{{ $value['station_type']}}</td>
                                <td>{{ $value['alert_value']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <h5 class="media-heading py-2"><strong>Assets</strong>&nbsp;<a href="{{ route('station_assets.create')}}" class="btn btn-search btn-indigo btn-sm pull-right"><i class="fa fa-plus"></i>&nbsp;Tambah Assets</a></h5>
        <div class="table-responsive ">
            <table class="table table-striped m-b-0 no-border">
                <thead>
                    <tr>
                        <th><strong>No.</strong></th>
                        <th><strong>Nama Asset</strong></th>
                        <th><strong>Merek</strong></th>
                        <th><strong>Tipe</strong></th>
                        <th><strong>Serial Number</strong></th>
                        <th><strong>Spesifikasi</strong></th>
                        <th><strong>Tahun Pengadaan</strong></th>
                        <th><strong>Foto</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($station_assets as $key => $value)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $value->asset_name}}</td>
                        <td>{{ $value->asset_brand}}</td>
                        <td>{{ $value->asset_type}}</td>
                        <td>{{ $value->asset_serial_number}}</td>
                        <td>{{ $value->asset_spesification}}</td>
                        <td>{{ $value->asset_year}}</td>
                        <td> <a href="javascript:;" class="img-btn" data-image="{{ $value->asset_imgae }}" data-toggle="modal" data-target="#imageModal"><img src="/{{ $value->asset_tumbnial }}" alt="" srcset=""></a></td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <h5 class="media-heading py-2"><strong>Riwayat Perawatan</strong>&nbsp; <a href="{{ route('station_history.create')}}" class="btn btn-search btn-indigo btn-sm pull-right"><i class="fa fa-plus"></i>&nbsp;Tambah Riwayat</a></h5>
        <div class="table-responsive ">
            <table class="table table-striped m-b-0 no-border">
                <thead>
                    <tr>
                        <th><strong>No.</strong></th>
                        <th><strong>Asset </strong></th>
                        <th><strong>Judul </strong></th>
                        <th><strong>Isi</strong></th>
                        <th><strong>Diinput Oleh</strong></th>
                        <th><strong>Tanggal Input</strong></th>
                        <th><strong>Diupdate Oleh</strong></th>
                        <th><strong>Tanggal Update</strong></th>
                        <th><strong>Foto</strong></th>
                        <th><strong>Edit</strong></th>
                        <th><strong>Delete</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($station_history as $key => $value)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $value->asset->asset_name ?? ''}}</td>
                        <td>{{ $value->history_title}}</td>
                        <td>{{ $value->history_body}}</td>
                        <td>{{ $value->creator->name}}</td>
                        <td>{{ $value->created_at}}</td>
                        <td>{{ $value->editor->name ?? ''}}</td>
                        <td>{{ $value->updated_at}}</td>
                        <td> <a href="javascript:;" class="img-btn" data-image="{{ $value->history_imgae }}" data-toggle="modal" data-target="#imageModal"><img src="/{{ $value->history_tumbnial }}" alt="" srcset=""></a></td>
                        <td>
                            <a href="{{ route('station_history.edit', $value['history_id'])  }}" class="btn btn-indigo btn-icon btn-circle btn-md"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
                            <form action="{{ route('station_history.destroy', $value['history_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon btn-circle btn-md"><i class="fa fa-trash"></i></button>
                            </form>

                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Assets</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-fluid">
                    <img src="" id="image-modal" alt="" srcset="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('scripts')

<script>
    $('.img-btn').click(function() {
        $('#image-modal').attr('src', '/' + $(this).data('image'))
    })

</script>
@endpush