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
                    <a href="{{ route('users.create')}}" class="btn btn-search btn-indigo"><i class="fa fa-plus"></i>&nbsp;Tambah Data</a>

                </div>
        </form>
        <div class="table-responsive">
            <table class="table" id="station-table">
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
                        <td>{{ $loop->iteration  }}</td>
                        @foreach($arrfield as $kf=> $vf)
                        @if($vf['form_type'] == 'text')
                        <td>{{$value[$kf]}}</td>
                        @elseif($vf['form_type'] == 'select')
                        <td>{{ $vf['keyvaldata'][$value[$kf]] }}</td>
                        @elseif($vf['form_type'] == 'password')
                        <td>****************</td>
                        @endif
                        @endforeach

                        <td>
                            <a href="{{ route('users.edit', $value['id'])  }}" class="btn btn-indigo btn-icon btn-circle btn-sm"><i class="fa fa-edit"></i></a>
                        </td>
                        <td>
                            <form action="{{ route('users.destroy', $value['id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon btn-circle btn-sm"><i class="fa fa-trash"></i></button>
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
</script>

@endpush