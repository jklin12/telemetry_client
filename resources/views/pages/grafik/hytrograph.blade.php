@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
<link rel="stylesheet" href="/assets/plugins/select2/dist/css/select2.min.css">
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->

<div class="panel panel-inverse">
    <div class="panel-body">
        <form action="" method="get" id="filter-form">
            <div class="row">
                <!--<div class="col-md-4">
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
                </div>-->
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Date : </label>
                        <div class="col-md-7">
                            <input id="reservationDate" type="text" name="date" class="form-control datepicker" value="{{ $filterDate }}" />
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

                <!--<div class="text-muted f-w-600 mt-2 mt-sm-0">compared to <span id="daterange-prev-date">24 Mar-30 Apr 2020</span></div>-->
            </div>
        </form>

        @if(!isset($data['datas']))
        <div class="">
            <div class="alert alert-warning fade show m-b-10">
                <span class="close" data-dismiss="alert">×</span>
                Maaf! Data belum tersedia.
            </div>
        </div>
        @endif

        <div id="container_chart"></div>
    </div>
</div>
@endsection



@push('scripts')
<script src="/vendor/datatables/buttons.server-side.js"></script>
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

    $(".datepicker").datepicker({
        format: 'yyyy-mm-dd',
        defaultDate: '<?php echo $filterDate ?>'
    }).on('changeDate', function(ev) {

        $('#filter-form').submit();
    });
    $('#interval').on('change', function(ev) {
        $('#filter-form').submit();
    });
    $('#interval option[value=<?php echo $filterInterval ?>]').attr('selected', 'selected');


    <?php if (isset($data['datas'])) { ?>


        Highcharts.chart('container_chart', {
            title: {
                text: '',
                align: 'left',
                style: {
                    display: 'none'
                }
            },

            xAxis: [{
                categories: <?php echo json_encode($data['label']) ?>,

            }],
            yAxis: {
                title: {
                    text: 'Continous Rainfall'
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
                <?php foreach ($data['datas'] as $key => $value) { ?> {
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