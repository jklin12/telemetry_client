<?php

namespace App\Http\Controllers;


use App\DataTables\RainfallbystationDataTable;
use App\Models\CurentRainfallModel;
use App\Models\Rainfall30Model;
use App\Models\Rainfall60Model;
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
        $curetnRainfall = CurentRainfallModel::where('rain_fall_date', $filterDate)->get();

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['filterDate'] = $filterDate;
        //$load['datas'] = $response->object();
        $load['datas'] = $curetnRainfall;
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

        $select .= "rain_fall_time as rt,rain_fall_10_minut,rain_fall_30_minute,rain_fall_1_hour,rain_fall_3_hour,rain_fall_6_hour,rain_fall_12_hour,rain_fall_24_hour,rain_fall_continuous,rain_fall_effective,rain_fall_effective_intensity,rain_fall_prev_working,rain_fall_working,rain_fall_working_24,rain_fall_remarks";

        if ($interval == 60) {
            $rainfall = Rainfall60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_60.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                ->where('station', $filterStation);
        } elseif ($interval == 30) {
            $rainfall = Rainfall30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_30.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                ->where('station', $filterStation);
        } elseif ($interval == 10) {
            $rainfall = RainfallModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                ->where('station', $filterStation);
        }

        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }
        //dd($rainfall->get()->toArray());

        $susunData = [];
        $susunGrafik = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');


            $susunData[$key] = $value;
            $susunData[$key]['rt'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');

            $susunGrafik['label'][$value['rt']] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            $susunGrafik['datas']['rc']['station'] = 'Rain Continous';
            $susunGrafik['datas']['rc']['value'][] = intval($value['rain_fall_continuous']);
            $susunGrafik['datas']['er']['station'] = 'Effective Rainfall';
            $susunGrafik['datas']['er']['value'][] = intval($value['rain_fall_effective']);
            $susunGrafik['datas']['rh']['station'] = 'Rain Houry';
            $susunGrafik['datas']['rh']['value'][] = intval($value['rain_fall_1_hour']);
            $susunGrafik['datas']['r6']['station'] = 'Rainfall 6 Hour';
            $susunGrafik['datas']['r6']['value'][] = intval($value['rain_fall_6_hour']);
            $susunGrafik['datas']['r12']['station'] = 'Rainfall 12 Hour';
            $susunGrafik['datas']['r12']['value'][] = intval($value['rain_fall_12_hour']);
            $susunGrafik['datas']['r24']['station'] = 'Rainfall 24 Hour';
            $susunGrafik['datas']['r24']['value'][] = intval($value['rain_fall_24_hour']);
        }
        //dd($susunGrafik);

        $stationList = StationModel::rightJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where('station_type',  'RG')
            ->groupBy('sch_data_station.station_id')
            ->get()->toArray();

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['susunGrafik'] = $susunGrafik;
        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['arr_field'] = $this->arrField();
        $load['station_list'] = $stationList;


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

        $select .= "rain_fall_time as rt ,rain_fall_continuous as average_rc,rain_fall_1_hour as average_rh";


        $rainfall = RainfallModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate);

        if ($interval == 60) {
            $rainfall = Rainfall60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_60.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate);
        } elseif ($interval == 30) {
            $rainfall = Rainfall30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_30.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate);
        } elseif ($interval == 10) {
            $rainfall = RainfallModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate);
        }
        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }


        $susunData = [];
        $susunGrafik = [];
        $arrDataByStation = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {

            $susunData['station'][$value['station']]['station_id'] = $value['station'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];

            $susunData['data'][$value['rt']]['date_time'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            $susunData['data'][$value['rt']]['datas'][$value['station_id']] = $value;

            $arrDataByStation[$value['station']]['rh'][$value['rt']] =  $value['average_rh'];
            $arrDataByStation[$value['station']]['rc'][$value['rt']] =  $value['average_rc'];

            $susunGrafik['label'][$value['rt']] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            $susunGrafik['datas'][$value['station']]['station'] = $value['station_name'];
            $susunGrafik['datas'][$value['station']]['value'][] = intval($value['average_rc']);
        }
        if (isset($susunData['data'])) {
            ksort($susunData['data']);
            $nweDatas = [];
            foreach ($susunData['data'] as $key => $value) {
                $nweDatas[$key]['date_time'] = $value['date_time'];
                foreach ($susunData['station'] as $keys => $values) {
                    $nweDatas[$key]['datas'][$keys] = $value['datas'][$keys] ?? [];
                }
            }
            unset($susunData['data']);
            $susunData['data'] = $nweDatas;
        }
       
        //dd($susunGrafik);

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
        $load['susunGrafik'] = $susunGrafik;
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
