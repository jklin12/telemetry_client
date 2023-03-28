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
        <div class="table-responsive">
            <table class="table" id="station-table">
                <thead>
                    <th class=" ">No.</th>

                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Foto</th>
                    <th>Delete</th>

                </thead>
                <tbody>
                    @foreach($datas as $key=> $value)
                    <tr>
                        <td>{{ $loop->iteration  }}</td>
                        <td>{{ $value->user->name}}</td>
                        <td>{{ $value->absen_time}}</td>
                        <td>{{ $value->latitude}}</td>
                        <td>{{ $value->longitude}}</td>
                        <td><img src="{{ asset($value->absen_file)}}" width="50" alt="" srcset=""></td>


                        <td>
                            <form action="{{ route('absen.destroy', $value->absen_id) }}" method="POST">
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