<?php

namespace App\Http\Controllers;

use App\Models\CurentRainfallModel;
use App\Models\FlowModel;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevelModel;
use App\Models\WireVibrationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {

        $title = 'Dashboard';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;



        $station = StationModel::get();

        $susunData = [];
        foreach ($station as $key => $value) {

            $susunData[$key]['type'] = 'Feature';
            $susunData[$key]['properties']['description'] = '<strong>' . $value->station_name . '</strong><p>' . $value->station_station_river . '<br>' . $value->station_equipment . '<br>' . $value->station_authority . '<br>' . $value->station_guardsman . '</p>';
            $susunData[$key]['properties']['icon'] = 'mountain-11';
            //$susunData[$key]['properties']['icon'] = $value->station_icon;
            $susunData[$key]['geometry']['type'] = 'Point';
            $susunData[$key]['geometry']['coordinates'][] = $this->dms_to_dec($value->station_long);
            $susunData[$key]['geometry']['coordinates'][] = doubleval('-' . $this->dms_to_dec($value->station_lat));
        }

        //dd($susunData);
        $load['datas'] = json_encode($susunData);

        return view('pages/dashboard/index', $load);
    }


    function dms_to_dec($dms)
    {

        $dms = stripslashes($dms);
        $parts = explode(' ', $dms);
        foreach ($parts as $key => $value) {
            $parts[$key] = preg_replace('/\D/', '', $value);
        }

        // parts: 0 = degree, 1 = minutes, 2 = seconds
        $d = isset($parts[0]) ? (float)$parts[0] : 0;
        $m = isset($parts[1]) ? (float)$parts[1] : 0;
        if (strpos($dms, ".") > 1 && isset($parts[2])) {
            $m = (float)($parts[1] . '.' . $parts[2]);
            unset($parts[2]);
        }
        $s = isset($parts[2]) ? (float)$parts[2] : 0;
        $dec = ($d + ($m / 60) + ($s / 3600));
        return $dec;
    }


    public function monitoring(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Monitoring Data';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;

        $curentRainfall['title'] = 'Current Rainfall';
        //$responseRainFall = Http::get('http://202.169.224.46:5000/curentRainfall');
        //$responseRainFall = Http::get('http://202.173.16.249:8000/curentRainfall');
        //$curentRainfall['data'] =  $responseRainFall->object();
        //dd($curentRainfall);
        $now = date('H:i:s');
        $prevHour = date('H:i:s', strtotime('-2 hour', strtotime($now)));
        //dd($now,$prevHour);

        $curentRainfall['data'] =  CurentRainfallModel::where('rain_fall_date', $filterDate)->get();
        //echo json_encode(array_values($finalJson));
        //dd($rainfallData,array_values($finalJson));

        $waterLevel['title'] = 'Water Level';
        $waterlevelQuery = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            ->whereRaw("water_level_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get()->toArray();

        $waterLevelData = [];

        foreach ($waterlevelQuery as $key => $value) {
            $waterLevelData['station'][$value['station']] = $value['station_name'];

            $waterLevelData['data'][$value['water_level_time']]['time'] = Carbon::parse($value['water_level_time'])->isoFormat('HH:mm');
            $waterLevelData['data'][$value['water_level_time']]['data'][] = $value['water_level_hight'];
        }
        $waterLevel['data'] = $waterLevelData;
        //dd($waterLevel);

        $wireVibration['title'] = 'Wire & Vibration Daily Report ';
        $wireVibrationQuery = WireVibrationModel::select(
            'station_id',
            'station',
            'station_name',
            'wire_vibration_date',
            'wire_vibration_time',
            'wire',
            'vibration',
        )
            ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
            ->where('wire_vibration_date', $filterDate)
            ->whereRaw("wire_vibration_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('wire_vibration_time')
            ->get()->toArray();

        $wireVibrationData = [];
        foreach ($wireVibrationQuery as $key => $value) {

            $wireVibrationData['station'][$value['station']] = $value['station_name'];
            $wireVibrationData['data'][$value['wire_vibration_time']]['date_time'] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH:mm');
            $wireVibrationData['data'][$value['wire_vibration_time']]['datas'][] = $value;
        }
        //dd($wireVibrationData);

        $wireVibration['data'] = $wireVibrationData;

        $flow['title'] = 'Flow Daily Report ';
        $flowQuer = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            ->whereRaw("flow_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get()->toArray();

        $flowData = [];
        foreach ($flowQuer as $key => $value) {
            $flowData['station'][$value['station']] = $value['station_name'];
            $flowData['data'][$value['flow_time']]['date_time'] = Carbon::parse($value['flow_time'])->isoFormat('HH:mm');
            $flowData['data'][$value['flow_time']]['datas'][] = $value['flow'];
        }
        //dd($flowData);
        $flow['data'] = $flowData;

        $station = StationModel::get();

        $stationData = [];
        foreach ($station as $key => $value) {

            $stationData[$key]['type'] = 'Feature';
            $stationData[$key]['properties']['title'] = $value->station_name;
            $stationData[$key]['properties']['description'] = '<strong>' . $value->station_name . '</strong><p>' . $value->station_station_river . '<br>' . $value->station_equipment . '<br>' . $value->station_authority . '<br>' . $value->station_guardsman . '</p>';
            $stationData[$key]['properties']['icon'] = 'mountain-11';
            //$susunData[$key]['properties']['icon'] = $value->station_icon;
            $stationData[$key]['geometry']['type'] = 'Point';
            $stationData[$key]['geometry']['coordinates'][] = $this->dms_to_dec($value->station_long);
            $stationData[$key]['geometry']['coordinates'][] = doubleval('-' . $this->dms_to_dec($value->station_lat));
        }

        //dd($stationData);


        $load['title'] = $title;
        $load['filterDate'] = $filterDate;
        $load['curentRainFall'] = $curentRainfall;
        $load['waterLevel'] = $waterLevel;
        $load['wireVibration'] = $wireVibration;
        $load['flow'] = $flow;
        $load['station'] = json_encode(array_values($stationData));
        //dd($load);
        return view('pages/dashboard/monitoring', $load);
    }

    public function portalData()
    {

        $filterDate = date('Y-m-d');
        $now = date('H:i:s');
        $prevHour = date('H:i:s', strtotime('-2 hour', strtotime($now)));

        $curentRainfall = CurentRainfallModel::where('rain_fall_date', $filterDate)->get();

        $rainfallJson = [];
        foreach ($curentRainfall as $key => $value) {
            $rainfallJson[$key][] = $key + 1;
            $rainfallJson[$key][] = $value->station;
            $rainfallJson[$key][] = $value->rain_fall_10_minut;
            $rainfallJson[$key][] = $value->rain_fall_30_minute;
            $rainfallJson[$key][] = $value->rain_fall_1_hour;
            $rainfallJson[$key][] = $value->rain_fall_3_hour;
            $rainfallJson[$key][] = $value->rain_fall_6_hour;
            $rainfallJson[$key][] = $value->rain_fall_12_hour;
            $rainfallJson[$key][] = $value->rain_fall_24_hour;
            $rainfallJson[$key][] = $value->rain_fall_continuous;
            $rainfallJson[$key][] = $value->rain_fall_effective;
            $rainfallJson[$key][] = $value->rain_fall_effective_intensity;
            $rainfallJson[$key][] = $value->rain_fall_prev_working;
            $rainfallJson[$key][] = $value->rain_fall_working;
            $rainfallJson[$key][] = $value->rain_fall_working_24;
            $rainfallJson[$key][] = $value->rain_fall_remarks;
        }

        $waterlevelQuery = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            ->whereRaw("water_level_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get();


        $susunDataWL = [];
        foreach ($waterlevelQuery as $key => $value) {
            $susunDataWL[$value->water_level_time]['time'] = $value->water_level_time;
            $susunDataWL[$value->water_level_time]['data'][$value->station] = $value->water_level_hight;
            //$susunDataWL[$value->water_level_time][] = $value->water_level_hight;
        }
        $wLJson  = [];
        $iWl = 1;
        foreach ($susunDataWL as $key => $value) {
            $wLJson[$key][] = $iWl;
            $wLJson[$key][] = $value['time'];
            foreach ($value['data'] as $kData => $vData) {
                $wLJson[$key][] = $vData;
            }
            $iWl++;
        }

        $wireVibrationQuery = WireVibrationModel::select(
            'station_id',
            'station',
            'station_name',
            'wire_vibration_date',
            'wire_vibration_time',
            'wire',
            'vibration',
        )
            ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
            ->where('wire_vibration_date', $filterDate)
            ->whereRaw("wire_vibration_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('wire_vibration_time')
            ->get();

        $susunDataWv = [];
        foreach ($wireVibrationQuery as $key => $value) {
            $susunDataWv[$value->wire_vibration_time]['time'] = $value->wire_vibration_time;
            $susunDataWv[$value->wire_vibration_time]['data'][$value->station]['wire'] = $value->wire;
            $susunDataWv[$value->wire_vibration_time]['data'][$value->station]['vibration'] = $value->vibration;
            //$susunDataWL[$value->water_level_time][] = $value->water_level_hight;
        }

        $wVirationJson  = [];
        $iWv = 1;
        foreach ($susunDataWv as $key => $value) {
            $wVirationJson[$key][] = $iWv;
            $wVirationJson[$key][] = $value['time'];
            foreach ($value['data'] as $kData => $vData) {
                $wVirationJson[$key][] = $vData['wire'];
                $wVirationJson[$key][] = $vData['vibration'];
            }
            $iWv++;
        }

        $flowQuer = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            ->whereRaw("flow_time BETWEEN '" . $prevHour . "' AND '" . $now . "'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get();

        $susunFlow = [];
        foreach ($flowQuer as $key => $value) {
            $susunFlow[$value->flow_time]['time'] = $value->flow_time;
            $susunFlow[$value->flow_time]['data'][$value->station] = $value->flow;
            //$susunDataWL[$value->water_level_time][] = $value->water_level_hight;
        }

        $flowJson  = [];
        $iFlow = 1;
        foreach ($susunFlow as $key => $value) {
            $flowJson[$key][] = $iFlow;
            $flowJson[$key][] = $value['time'];
            foreach ($value['data'] as $kData => $vData) {
                $flowJson[$key][] = $vData;
            }
            $iFlow++;
        }
        echo json_encode(
            [
                'curent_rainfall' => array_values($rainfallJson),
                'water_level' => array_values($wLJson),
                'wire_vibration' => array_values($wVirationJson),
                'flow' => array_values($flowJson),
            ]
        );
    }

    public function alertData()
    {

        $random_number_array = range(0, 23);
        shuffle($random_number_array);
        $random_number_array = array_slice($random_number_array, 0, rand(0, 5));

        $data = StationModel::whereIn('station_id', $random_number_array)->get();

        $susunData = [];
        foreach ($data as $key => $value) {
            $susunData[$key]['class'] = $key % 2 == 0 ? 'warning-popup' : 'danger-popup';
            $susunData[$key]['element'] =   $key % 2 == 0 ? '<strong>Plawangan</strong><br><p>Alert Warning</p>' : '<strong>Plawangan</strong><br><p>Alert Danger</p>' ;
            $susunData[$key]['coordinates'][] = $this->dms_to_dec($value->station_long);
            $susunData[$key]['coordinates'][] = doubleval('-' . $this->dms_to_dec($value->station_lat));
        }
        echo json_encode($susunData);
    }
}
