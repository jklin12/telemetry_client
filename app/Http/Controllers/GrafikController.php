<?php

namespace App\Http\Controllers;

use App\Models\FlowModel;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevelModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrafikController extends Controller
{
    public function judment(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Judment Graph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' '.$station->station_name;
        
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $rainfall = RainfallModel::leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation)
            //->groupBy('station') 
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        $data['label'] = [];
        $data['rh'] = [];
        $data['rc'] = [];
        foreach ($rainfall as $key => $value) {
            //if ($value['rain_fall_1_hour'] && $value['rain_fall_continuous']) {
                $data['rh'][] = doubleval($value['rain_fall_1_hour']);
                $data['rc'][] = doubleval($value['rain_fall_continuous']);
                $data['label'][] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH:mm');
            //}
        }
        //dd($data);


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/judment', $load);
    }

    public function hydrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' '.$station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $waterlevel = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            ->where('station_id', $filterStation)
            //->groupBy('station')
            ->orderBy('water_level_time')
            ->get()->toArray();

        $data['label'] = [];
        $data['water_level'] = [];
        $data['flow'] = [];
        foreach ($waterlevel as $key => $value) {
            //if ($value['water_level_hight']) {
                $data['water_level'][] = doubleval($value['water_level_hight']);
                $data['label'][] = Carbon::parse($value['water_level_time'])->isoFormat('HH:mm');
            //}
        }

        $flow = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->where('flow_date', '2022-12-06')
            //->where('station_id', 10)
            ->where('station_id', $filterStation)
            ->orderBy('flow_time')
            ->get()->toArray();

        foreach ($flow as $key => $value) {
            if ($value['flow']) {
                $data['flow'][] = doubleval($value['flow']);
            }
        }


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/hydrograph', $load);
    }

    public function hytrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' '.$station->station_name;
        
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $rainfall = RainfallModel::leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation)
            //->groupBy('station') 
            ->orderBy('rain_fall_time')
            ->get()->toArray();

        $data['label'] = [];
        $data['rh'] = [];
        $data['rc'] = [];
        foreach ($rainfall as $key => $value) {
            //if ($value['rain_fall_1_hour'] && $value['rain_fall_continuous']) {
                $data['rh'][] = doubleval($value['rain_fall_1_hour']);
                $data['rc'][] = doubleval($value['rain_fall_continuous']);
                $data['label'][] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH:mm');
            //}
        }

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/hytrograph', $load);
    }
}
