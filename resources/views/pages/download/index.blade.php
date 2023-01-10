@extends('layouts.default')

@section('title', 'Mt. Merapi Telemetry System')

@push('css')
@endpush

@section('content')
<!-- begin page-header -->
<h1 class="page-header">{{ $title }} <small>{{ $subTitle }}</small></h1>
<!-- end page-header -->

<div class="panel panel-inverse">
    <div class="panel-body">
        <form action="{{ route('download.store')}}" method="post" id="filter-form" class="align-item-center">
            @csrf
            <div class="col-md-4">
                <div class="form-group row mb-2">
                    <label class="col-form-label col-md-4">Measuring Item :</label>
                    <div class="col-md-7">
                        <select class="form-control " id="mesuring" name="mesuring" required>
                            <option value=""> </option>
                            <option value="RG">Rainfall</option>
                            <option value="WL">Water Level</option>
                            <option value="MF">Flow</option>
                            <option value="WV">Wire & Vibration</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row mb-2">
                    <label class="col-form-label col-md-4">Station :</label>
                    <div class="col-md-7">
                        <select class="default-select2 form-control " id="station" name="station" required>

                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row mb-2">
                    <label class="col-form-label col-md-4">Interval :</label>
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
                <div class="form-group row mb-2">
                    <label class="col-form-label col-md-4">Date Start :</label>
                    <div class="col-md-7">
                        <input id="date_start" type="text" name="date_start" class="form-control datepicker" value="" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row mb-2">
                    <label class="col-form-label col-md-4">Date END :</label>
                    <div class="col-md-7">
                        <input id="date_end" type="text" name="date_end" class="form-control datepicker" value="" />
                    </div>
                </div>
            </div>

            <button type="sumbit" class="btn btn-indigo ">Vreate CSV</button>


        </form>

    </div>
    <div id="container_chart"></div>
</div>
@endsection



@push('scripts')


<script>
    ///alert(Date())
    $(".datepicker").datepicker({
        format: 'yyyy-mm-dd', 
        orientation: "bottom",
    })
    $('#mesuring').on('change', function() {

        $.ajax({
            url: "<?php echo route('station.find') ?>",
            type: 'POST', // http method
            data: {
                _token: '<?php echo csrf_token() ?>',
                type: $(this).val()
            },
            success: function(data) {
                var json = JSON.parse(data)
                //alert(json)
                $('#station').html(json)
            }

        })
    })
</script>


@endpush