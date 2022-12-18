<?php

namespace App\Http\Controllers;


use App\DataTables\RainfallbystationDataTable;
use App\Models\RainfallModel;
use App\Models\StationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class RainfallController extends Controller
{
    public function current(Request $request)
    {


        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');

        $title = 'Current Rainfall';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        //$response = Http::get('http://202.169.224.46:5000/curentRainfall');
        //$response = Http::get('http://202.173.16.249:8000/curentRainfall');


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['filterDate'] = $filterDate;
        //$load['datas'] = $response->object();
        $load['datas'] = [];
        //dd($load['datas']);
        $load['arr_field'] = $this->arrField();

        return view('pages/rainfall/current', $load);
    }

    public function byStation(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $interval = $request->has('interval') ? $request->get('interval') : 60;
        $title = 'Rainfall Report ';
        $subTitle = 'by station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        //dd(route('rainfall'));

        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "rain_fall_time as rt,rain_fall_10_minut,rain_fall_30_minute,rain_fall_1_hour,rain_fall_3_hour,rain_fall_6_hour,rain_fall_12_hour,rain_fall_24_hour,rain_fall_continuous,rain_fall_effective,rain_fall_effective_intensity,rain_fall_prev_working,rain_fall_working,rain_fall_working_24,rain_fall_remarks";
        } elseif ($interval == 30) {
            $select .= 'DATE_FORMAT(rain_fall_time, "%H:") as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_10_minut),3) as rain_fall_10_minut,ROUND(AVG(rain_fall_30_minute),3) as rain_fall_30_minute,ROUND(AVG(rain_fall_1_hour),3) as rain_fall_1_hour,ROUND(AVG(rain_fall_3_hour),3) as rain_fall_3_hour,ROUND(AVG(rain_fall_6_hour),3) as rain_fall_6_hour,ROUND(AVG(rain_fall_12_hour),3) as rain_fall_12_hour,ROUND(AVG(rain_fall_24_hour),3) as rain_fall_24_hour,ROUND(AVG(rain_fall_continuous),3) as rain_fall_continuous,ROUND(AVG(rain_fall_effective),3) as rain_fall_effective,ROUND(AVG(rain_fall_effective_intensity),3) as rain_fall_effective_intensity,ROUND(AVG(rain_fall_prev_working),3) as rain_fall_prev_working,ROUND(AVG(rain_fall_working),3) as rain_fall_working,ROUND(AVG(rain_fall_working_24),3) as rain_fall_working_24, rain_fall_remarks';
            $group = 'station,CONCAT(
                HOUR(rain_fall_time),
                IF("30">MINUTE(rain_fall_time), "00", "30")
               )';
        } elseif ($interval == 60) {
            $select .= "rain_fall_time as rt,ROUND(AVG(rain_fall_10_minut),3) as rain_fall_10_minut,ROUND(AVG(rain_fall_30_minute),3) as rain_fall_30_minute,ROUND(AVG(rain_fall_1_hour),3) as rain_fall_1_hour,ROUND(AVG(rain_fall_3_hour),3) as rain_fall_3_hour,ROUND(AVG(rain_fall_6_hour),3) as rain_fall_6_hour,ROUND(AVG(rain_fall_12_hour),3) as rain_fall_12_hour,ROUND(AVG(rain_fall_24_hour),3) as rain_fall_24_hour,ROUND(AVG(rain_fall_continuous),3) as rain_fall_continuous,ROUND(AVG(rain_fall_effective),3) as rain_fall_effective,ROUND(AVG(rain_fall_effective_intensity),3) as rain_fall_effective_intensity,ROUND(AVG(rain_fall_prev_working),3) as rain_fall_prev_working,ROUND(AVG(rain_fall_working),3) as rain_fall_working,ROUND(AVG(rain_fall_working_24),3) as rain_fall_working_24,rain_fall_remarks";
            $group = 'station,HOUR(rain_fall_time)';
        }

        $rainfall = RainfallModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation);

        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }
        //dd($rainfall->get()->toArray());

        $susunData = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');
            if ($interval == 30) {
                $times =  ($value['hour'] . $value['rt']);
                $susunData[$key] = $value;
                $susunData[$key]['rt'] = $times;
            } else {
                
                $susunData[$key] = $value;
                $susunData[$key]['rt'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');;
            }
        }
        //dd($susunData);
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['arr_field'] = $this->arrField();
        $load['station_list'] = StationModel::get()->toArray();


        return view('pages/rainfall/index', $load);
    }

    public function daily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Daily Rainfall Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;



        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "rain_fall_time as rt ,rain_fall_1_hour as average_rc,rain_fall_1_hour as average_rh";
        } elseif ($interval == 30) {
            $select .= 'DATE_FORMAT(rain_fall_time, "%H:") as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh';
            $group = 'station,CONCAT(
                HOUR(rain_fall_time),
                IF("30">MINUTE(rain_fall_time), "00", "30")
               )';
        } elseif ($interval == 60) {
            $select .= "rain_fall_time as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh";
            $group = 'station,HOUR(rain_fall_time)';
        }

        $rainfall = RainfallModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate);

        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }


        $susunData = [];


        $arrDataByStation = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {

            $susunData['station'][$value['station']]['station_id'] = $value['station'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            if ($interval == 30) {
                $times =  date($value['hour'] . $value['rt']);
                $susunData['data'][$value['hour'] . $value['rt']]['date_time'] = $times;
                $susunData['data'][$value['hour'] . $value['rt']]['datas'][] = $value;

                $arrDataByStation[$value['station']]['rh'][$value['hour'] . $value['rt']] =  $value['average_rh'];
                $arrDataByStation[$value['station']]['rc'][$value['hour'] . $value['rt']] =  $value['average_rc'];
            } else {
                $susunData['data'][$value['rt']]['date_time'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
                $susunData['data'][$value['rt']]['datas'][] = $value;
                $arrDataByStation[$value['station']]['rh'][$value['rt']] =  $value['average_rh'];
                $arrDataByStation[$value['station']]['rc'][$value['rt']] =  $value['average_rc'];
            }
        }
        //dd($susunData);
        $avergaeRh = [];
        $avergaeRc = [];
        $maxRh = [];
        $maxRc = [];
        $timeRh = [];
        $timeRc = [];
        if (isset(($susunData['station']))) {
            foreach ($susunData['station'] as $key => $value) {

                $avergaeRh[$key] = round(array_sum($arrDataByStation[$value['station_id']]['rh']) / count($arrDataByStation[$value['station_id']]['rh']), 3);
                $avergaeRc[$key] = round(array_sum($arrDataByStation[$value['station_id']]['rc']) / count($arrDataByStation[$value['station_id']]['rc']), 3);
                $maxRh[$key] = max($arrDataByStation[$value['station_id']]['rh']);
                $maxRc[$key] = max($arrDataByStation[$value['station_id']]['rc']);
                $timeRh[$key] = array_search(max($arrDataByStation[$value['station_id']]['rh']), $arrDataByStation[$value['station_id']]['rh']);
                $timeRc[$key] = array_search(max($arrDataByStation[$value['station_id']]['rc']), $arrDataByStation[$value['station_id']]['rc']);
            }
        }


        $summaryData['average']['rh'] = $avergaeRh;
        $summaryData['average']['rc'] = $avergaeRc;
        $summaryData['max']['rh'] = $maxRh;
        $summaryData['max']['rc'] = $maxRc;
        $summaryData['time']['rh'] = $timeRh;
        $summaryData['time']['rc'] = $timeRc;

        //dd($summaryData);

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;
        $load['summaryData'] = $summaryData;
        $load['filterInterval'] = $interval;


        return view('pages/rainfall/daily', $load);
    }

    protected function arrField()
    {
        return [
            /*'station_name' => [
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
            ],*/
            'rt' => [
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
