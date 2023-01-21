@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="/assets/plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="/assets/plugins/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css" rel="stylesheet" />

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
                        <label class="col-form-label col-md-4">Station :</label>
                        <div class="col-md-7">
                            <select class="default-select2 form-control " id="station" name="station" required>
                                <option value="">-- Pilih Station --</option>
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
                        <label class="col-form-label col-md-4">Display Interval :</label>
                        <div class="col-md-7">
                            <select class="form-control " id="interval" name="interval" required>
                                <option value="10">10 Minutes</option>
                                <option value="30">30 Minutes</option>
                                <option value="60">1 Hour</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Date : </label>
                        <div class="col-md-7">
                            <input id="datepicker" type="text" name="date" class="form-control datepicker" value="{{ $filterDate }}" />
                        </div>
                    </div>
                </div>


                <!--<div class="text-muted f-w-600 mt-2 mt-sm-0">compared to <span id="daterange-prev-date">24 Mar-30 Apr 2020</span></div>-->
            </div>
        </form>

        <div class="table-responsive table-striped">
            @if($datas)
            <table id="data-table-fixed-header" class="table table-striped table-bordered table-td-valign-middle display compact">
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
                        <td class="text-center">{{$value[$kf]}}</td>
                        @endforeach

                    </tr>
                    @empty

                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Average</th>
                        @foreach($summaryData as $key => $value)
                        <th class="text-center">{{$value['average']}}</th>
                        @endforeach
                        
                    </tr>
                    <tr>
                        <th colspan="2">MAX</th>
                        @foreach($summaryData as $key => $value)
                        <th class="text-center">{{$value['max']}}</th>
                        @endforeach
                        
                    </tr>
                    <tr>
                        <th colspan="2">Time</th>
                        @foreach($summaryData as $key => $value)
                        <th class="text-center">{{$value['time']}}</th>
                        @endforeach
                        
                    </tr>
                </tfoot>

            </table>
            @else
            <div class="">
                <div class="alert alert-warning fade show m-b-10">
                    <span class="close" data-dismiss="alert">×</span>
                    Maaf! Data belum tersedia.
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<div class="panel panel-inverse">
    <div class="panel-body">
        <div id="container_chart"></div>
    </div>
</div>
@endsection



@push('scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="/assets/plugins/datatables.net-fixedheader-bs4/js/fixedHeader.bootstrap4.min.js"></script>
<script src="/assets/plugins/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="/assets/plugins/select2/dist/js/select2.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    ///alert(Date())

    $(".default-select2").select2().on('select2:select', function(e) {
        var data = e.params.data;
        $('#filter-form').submit();
    }).val(<?php echo $filterStation ?>).trigger('change');

    $("#datepicker").datepicker({
        format: 'yyyy-mm-dd',
        defaultDate: '<?php echo $filterDate ?>'
    }).on('changeDate', function(ev) {

        $('#filter-form').submit();
    });

    $('#interval').on('change', function(ev) {
        $('#filter-form').submit();
    });
    $('#interval option[value=<?php echo $filterInterval ?>]').attr('selected', 'selected');
    $('#data-table-fixed-header').DataTable({
        lengthMenu: [20, 40, 60],
        fixedHeader: {
            header: true,
            headerOffset: $('#header').height(),
            footer: true
        },
        dom: 'Bfrtip',
        buttons: [
            @auth {
                extend: 'csv',
                className: 'btn btn-indigo '
            },
            {
                extend: 'excel',
                className: 'btn btn-indigo '
            },
            @endauth {
                extend: 'pdf',
                className: 'btn btn-indigo ',
                orientation: 'landscape',

            },

        ],
        paging: false,
        ordering: false,
        searching: false,
        responsive: true
    });


    <?php if (isset($susunGrafik['datas'])) { ?>
        Highcharts.chart('container_chart', {
            title: {
                text: 'Flow Chart',
                align: 'center',
            },
            subtitle: {
                text: '<?php echo $subTitle ?>'
            },
            xAxis: [{
                categories: <?php echo json_encode(array_values($susunGrafik['label'])) ?>,

            }],
            yAxis: {
                title: {
                    text: 'Rainfall mm/h'
                }
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                }
            },

            series: [
                <?php foreach ($susunGrafik['datas'] as $key => $value) { ?> {
                        name: '<?php echo $value['station'] ?>',
                        data: <?php echo json_encode($value['value']) ?>
                    },

                <?php } ?>
            ],
            tooltip: {
                shared: true
            },
            responsive: {
                rules: [{
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
    <?php } ?>
</script>


@endpush