<?php

namespace App\Http\Controllers;

use App\Models\FlowModel;
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
        $responseRainFall = Http::get('http://202.169.224.46:5000/curentRainfall');
        $curentRainfall['data'] =  $responseRainFall->object();

        $waterLevel['title'] = 'Water Level';
        $waterlevelQuery = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get()->toArray();

        $waterLevelData = [];
        $arrDataByStation = [];
        foreach ($waterlevelQuery as $key => $value) {
            $waterLevelData['station'][$value['station']]['station_id'] = $value['station_id'];
            $waterLevelData['station'][$value['station']]['station_name'] = $value['station_name'];
            $waterLevelData['data'][$value['water_level_time']]['date_time'] = Carbon::parse($value['water_level_time'])->isoFormat('HH::mm');
            $waterLevelData['data'][$value['water_level_time']]['datas'][] = $value;
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
        )->where('wire_vibration_date', $filterDate)
            ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('wire_vibration_time')
            ->get()->toArray();

        $wireVibrationData = [];
        foreach ($wireVibrationQuery as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $wireVibrationData['station'][$value['station']]['station_name'] = $value['station_name'];
            $wireVibrationData['data'][$value['wire_vibration_time']]['date_time'] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH::mm');
            $wireVibrationData['data'][$value['wire_vibration_time']]['datas'][] = $value;
        }

        $wireVibration['data'] = $wireVibrationData;

        $flow['title'] = 'Flow Daily Report ';
        $flowQuer = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get()->toArray();

        $flowData = [];
        foreach ($flowQuer as $key => $value) {
            $flowData['station'][$value['station']]['station_id'] = $value['station_id'];
            $flowData['station'][$value['station']]['station_name'] = $value['station_name'];
            $flowData['data'][$value['flow_time']]['date_time'] = Carbon::parse($value['flow_time'])->isoFormat('HH::mm');
            $flowData['data'][$value['flow_time']]['datas'][] = $value;
        }
        $flow['data'] = $flowData;

        $load['title'] = $title;
        $load['filterDate'] = $filterDate;
        $load['curentRainFall'] = $curentRainfall;
        $load['waterLevel'] = $waterLevel;
        $load['wireVibration'] = $wireVibration;
        $load['flow'] = $flow;
        //dd($load);

        return view('pages/dashboard/monitoring', $load);
    }
}
