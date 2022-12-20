<?php

namespace App\Http\Controllers;

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
        $prevHour = date('H:i:s',strtotime('-1 hour', strtotime($now)));
        //dd($now,$prevHour);

        $rainfall = RainfallModel::select('station', 'rain_fall_date', 'rain_fall_time', 'station_name', 'rain_fall_1_hour', 'rain_fall_continuous')
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->whereRaw("rain_fall_time BETWEEN '".$prevHour."' AND '".$now."'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        $rainfallData = [];
        foreach ($rainfall as $key => $value) {
            $rainfallData['station'][$value['station']]['station_id'] = $value['station'];
            $rainfallData['station'][$value['station']]['station_name'] = $value['station_name'];
            $rainfallData['station'][$value['station']]['data']['rh'][$value['rain_fall_time']] = $value['rain_fall_1_hour'];
            $rainfallData['station'][$value['station']]['data']['rc'][$value['rain_fall_time']] = $value['rain_fall_continuous'];
            $rainfallData['data'][$value['rain_fall_time']] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH:mm');
        }
        $curentRainfall['data'] =  $rainfallData;
        //dd($rainfallData);

        $waterLevel['title'] = 'Water Level';
        $waterlevelQuery = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            ->whereRaw("water_level_time BETWEEN '".$prevHour."' AND '".$now."'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get()->toArray();

        $waterLevelData = [];

        foreach ($waterlevelQuery as $key => $value) {
            $waterLevelData['station'][$value['station']]['station_id'] = $value['station_id'];
            $waterLevelData['station'][$value['station']]['station_name'] = $value['station_name'];
            $waterLevelData['station'][$value['station']]['data'][$value['water_level_time']] = $value['water_level_hight'];

            $waterLevelData['data'][$value['water_level_time']] = Carbon::parse($value['water_level_time'])->isoFormat('HH:mm');
        }
        $waterLevel['data'] = $waterLevelData;

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
            ->whereRaw("wire_vibration_time BETWEEN '".$prevHour."' AND '".$now."'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('wire_vibration_time')
            ->get()->toArray();

        $wireVibrationData = [];
        foreach ($wireVibrationQuery as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $wireVibrationData['station'][$value['station']]['station_id'] = $value['station_id'];
            $wireVibrationData['station'][$value['station']]['station_name'] = $value['station_name'];
            $wireVibrationData['station'][$value['station']]['data'][$value['wire_vibration_time']]['wire'] = $value['wire'];
            $wireVibrationData['station'][$value['station']]['data'][$value['wire_vibration_time']]['vibration'] = $value['vibration'];
            $wireVibrationData['data'][$value['wire_vibration_time']] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH:mm');
        }
        //dd($wireVibrationData);

        $wireVibration['data'] = $wireVibrationData;

        $flow['title'] = 'Flow Daily Report ';
        $flowQuer = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            ->whereRaw("flow_time BETWEEN '".$prevHour."' AND '".$now."'")
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get()->toArray();

        $flowData = [];
        foreach ($flowQuer as $key => $value) {
            $flowData['station'][$value['station']]['station_id'] = $value['station_id'];
            $flowData['station'][$value['station']]['station_name'] = $value['station_name'];
            $flowData['station'][$value['station']]['data'][$value['flow_time']] = $value['flow'];
            $flowData['data'][$value['flow_time']] = Carbon::parse($value['flow_time'])->isoFormat('HH:mm');
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
}
