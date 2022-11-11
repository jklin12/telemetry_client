<?php

namespace App\Http\Controllers\Api;

use App\Models\FlowModel;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevelModel;
use App\Models\WireVibrationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataController extends BaseController
{
    public function stationList(Request $request)
    {


        $title = "Station List";
        $station = StationModel::paginate(50);

        return $this->sendResponse($station, $title . ' data found');
    }

    public function curentRainFall()
    {
        //$filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');

        $title = 'Current Rainfall';
        $response = Http::get('http://202.169.224.46:5000/curentRainfall');

        return $this->sendResponse($response->object(), $title . ' data found');
    }

    public function rainfallByStation(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $title = 'Rainfall Report ';
        $subTitle = 'by station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $rainfall = RainfallModel::leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        $susunData = [];
        foreach ($rainfall as $key => $value) {
            $susunData[$key] = $value;
            $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');;;
            $susunData[$key]['rain_fall_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['rainfall'] = $susunData;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function dailyRainFall(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Daily Rainfall Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;

        $rainfall = RainfallModel::select('station', 'rain_fall_date', 'rain_fall_time', 'station_name', 'rain_fall_1_hour', 'rain_fall_continuous')
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        $stationData  = [];
        $rainfallData = [];

        foreach ($rainfall as $key => $value) {
            $stationData[$value['station']]['station_name'] = $value['station_name'];
            $rainfallData[$value['rain_fall_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            $rainfallData[$value['rain_fall_time']]['datas'][] = $value;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['station'] = array_values($stationData);
        $load['rainfall'] = array_values($rainfallData);

        return $this->sendResponse($load, $title . ' data found');
    }

    public function waterLevel(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Water Level Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $waterlevel = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get()->toArray();

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['water_level_time']]['date_time'] = Carbon::parse($value['water_level_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['water_level_time']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['water_level_hight'];
        }

        $avergae = [];
        $max = [];
        if (isset(($susunData['station']))) {
            foreach ($susunData['station'] as $key => $value) {
                $avergae[$key] = round(array_sum($arrDataByStation[$value['station_id']]) / count($arrDataByStation[$value['station_id']]), 3);
                $max[$key] = max($arrDataByStation[$value['station_id']]);
            }
        }
        //print_r($max);die;
        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function wireVibration(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Wire & Vibration Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $wireVibration = WireVibrationModel::select(
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



        $susunData = [];

        foreach ($wireVibration as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['wire_vibration_time']]['date_time'] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['wire_vibration_time']]['datas'][] = $value;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function flow(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Water Level Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $waterlevel = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get()->toArray();

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['flow_time']]['date_time'] = Carbon::parse($value['flow_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['flow_time']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['flow'];
        }

        //dd($arrDataByStation);

        $avergae = [];
        $max = [];
        if (isset(($susunData['station']))) {
            foreach ($susunData['station'] as $key => $value) {
                $avergae[$key] = round(array_sum($arrDataByStation[$value['station_id']]) / count($arrDataByStation[$value['station_id']]), 3);
                $max[$key] = max($arrDataByStation[$value['station_id']]);
            }
        }
        //print_r($max);die;
        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;

        return $this->sendResponse($load, $title . ' data found');
    }
}
