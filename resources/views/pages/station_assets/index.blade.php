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

    .dataTables_filter {
        display: none;
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
                        <input id="searchbox" type="text" name="q" class="form-control" placeholder="Cari...">

                    </div>
                </div>
                <div class="col-sm">
                    <button type="submit" class="btn btn-search m-r-3"><i class="fa fa-search"></i></button>
                    <a href="{{ route('station_assets.create')}}" class="btn btn-search btn-indigo"><i class="fa fa-plus"></i>&nbsp;Tambah Data</a>

                </div>
        </form>
        <div class="table-responsive table-striped">
            <table class="table" id="">
                <thead>
                    <th class=" ">No.</th>
                    @foreach($arrfield as $kf=> $vf)
                    <th class="">{{$vf['label']}}</th>
                    @endforeach
                    <th>Edit</th>
                    <th>Delete</th>

                </thead>
                <tbody>
                    @foreach($datas as $key=> $value)
                    <tr>
                        <td>{{ ($datas->currentpage()-1) * $datas->perpage() + $loop->index + 1 }}</td>
                        @foreach($arrfield as $kf=> $vf)
                        @if($vf['form_type'] == 'text' || $vf['form_type'] == 'area')
                        <td>{{$value[$kf]}}</td>
                        @elseif($vf['form_type'] == 'year')
                        <td>{{$value[$kf]}}</td>
                        @elseif($vf['form_type'] == 'file')
                        <td> <a href="javascript:;" class="img-btn" data-image="{{ $value->asset_imgae }}" data-toggle="modal" data-target="#imageModal"><img src="{{ $value->asset_tumbnial }}" alt="" srcset=""></a></td>
                        @elseif($vf['form_type'] == 'color')
                        <td><span class="badge badge-default badge-square" style="background-color: {{$value[$kf]}} ;color: white;">{{$value[$kf]}}</span></td>
                        @elseif($vf['form_type'] == 'select' || $vf['form_type'] == 'select2' )
                        <td>{{ $vf['keyvaldata'][$value[$kf]] }}</td>
                        @elseif($vf['form_type'] == 'password')
                        <td>****************</td>
                        @endif
                        @endforeach

                        <td>
                            <a href="{{ route('station_assets.edit', $value['assets_id'])  }}" class="btn btn-indigo btn-icon btn-circle btn-md"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
                            <form action="{{ route('station_assets.destroy', $value['assets_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon btn-circle btn-md"><i class="fa fa-trash"></i></button>
                            </form>

                        </td>

                    </tr>

                    @endforeach
                </tbody>
            </table>
            {{ $datas->links() }}

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
                    <img src="" id="image-modal" alt="" srcset=""  class="img-fluid">
                </div>
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

<script>
    var table = $('#station-table').DataTable({
        "paging": false,
        "info": false,
        "search": false,
    })

    $('#searchbox').keyup(function() {
        table.search($(this).val()).draw();
    })

    $('.img-btn').click(function(){
        
        $('#image-modal').attr('src',$(this).data('image'))
    })
</script>

@endpush