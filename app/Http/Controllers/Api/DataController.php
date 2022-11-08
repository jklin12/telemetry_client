<?php

namespace App\Http\Controllers\API;
 
 use App\Models\RainfallModel;
use App\Models\StationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataController extends BaseController
{
    public function stationList(Request $request)
    {


        $title = "Station List";
        $station = StationModel::paginate(10);

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
}
