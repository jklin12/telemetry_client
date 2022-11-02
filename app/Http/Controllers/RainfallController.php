<?php

namespace App\Http\Controllers;


use App\DataTables\RainfallbystationDataTable;
use App\Models\RainfallModel;
use App\Models\StationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RainfallController extends Controller
{
    public function byStation(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $title = 'Rainfall Report ';
        $subTitle = 'by station '.Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        //dd(route('rainfall'));


        $rainfall = RainfallModel::
            leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('rain_fall_time')
            ->get()->toArray();
        $susunData=[];
        foreach ($rainfall as $key => $value) {
            $susunData[$key] = $value;
            $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');;;
            $susunData[$key]['rain_fall_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['arr_field'] = $this->arrField();
        $load['station_list'] = StationModel::get()->toArray();


        return view('pages/rainfall/index', $load);
    }

    public function daily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Daily Rainfall Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;

        //echo $filterDate;die;

        $rainfall = RainfallModel::select('station', 'rain_fall_date', 'rain_fall_time', 'station_name', 'rain_fall_1_hour', 'rain_fall_continuous')
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        //dd($rainfall);


        $susunData = [];
        #foreach ($rainfall as $key => $value) {
        #    $susunData[$value['station']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
        #    $susunData[$value['station']]['station_name'] = $value['station_name'];
        #    $susunData[$value['station']][] = $value;
        #}

        foreach ($rainfall as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['rain_fall_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['rain_fall_time']]['datas'][] = $value;
        }

        //dd($susunData);
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;


        return view('pages/rainfall/daily', $load);
    }

    protected function arrField()
    {
        return [
            'station_name' => [
                'label' => 'Station',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'rain_fall_date' => [
                'label' => 'Date',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'rain_fall_time' => [
                'label' => 'Time',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_10_minut' => [
                'label' => '10-min Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_30_minute' => [
                'label' => '30-min Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                //'keyvaldata' => $this->arrStatus
            ],
            'rain_fall_1_hour' => [
                'label' => 'Hourly Rainfall',
                'orderable' => false,
                'searchable' => true,
                'form_type' => 'text',

            ],
            'rain_fall_3_hour' => [
                'label' => '3-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                //'keyvaldata' => $this->arrPiStatus
            ],
            'rain_fall_6_hour' => [
                'label' => '6-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'rain_fall_12_hour' => [
                'label' => '12-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'rain_fall_24_hour' => [
                'label' => '24-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_continuous' => [
                'label' => 'Continous Rainfall',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_effective' => [
                'label' => 'Effective Rainfall',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_effective_intensity' => [
                'label' => 'Effective Intensity',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_prev_working' => [
                'label' => 'Previous Working',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_working' => [
                'label' => 'Working Rainfal',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_working_24' => [
                'label' => 'Working Rainfall (half-life:24h)',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_remarks' => [
                'label' => 'Remarks',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
        ];
    }
}
