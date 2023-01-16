<?php

namespace App\Http\Controllers;

use App\Models\Flow30Model;
use App\Models\Flow60Model;
use App\Models\FlowModel;
use App\Models\Rainfall30Model;
use App\Models\Rainfall60Model;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevel30Model;
use App\Models\WaterLevel60Model;
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
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Judment Graph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' ' . $station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "rain_fall_time as rt ,rain_fall_continuous as average_rc,rain_fall_1_hour as average_rh";
        } elseif ($interval == 30) {
            $select .= 'HOUR(rain_fall_time) as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_continuous),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh';
            $group = 'station,CONCAT(hour,rt)';
        } elseif ($interval == 60) {
            $select .= "rain_fall_time as rt,ROUND(AVG(rain_fall_continuous),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh";
            $group = 'station,HOUR(rain_fall_time)';
        }

        $rainfall = RainfallModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate)
            ->where('station', $filterStation)
            //->groupBy('station') 
            ->orderBy('rain_fall_time');
        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }

        //dd($rainfall->get()->toArray());
        $data['label'] = [];
        $data['rh'] = [];
        $data['rc'] = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            //if ($value['rain_fall_1_hour'] && $value['rain_fall_continuous']) {
            $data['rh'][] = doubleval($value['average_rh']);
            $data['rc'][] = doubleval($value['average_rc']);
            if ($interval == 30) {
                $data['label'][] = $value['hour'] . ':' . $value['rt'];
            } else {
                $data['label'][] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            }

            //}
        }
        //dd($data);

        $stationList = StationModel::rightJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where('station_type',  'RG')
            ->get()->toArray();

        //dd($stationList);

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = $stationList;
        $load['data'] = $data;

        dd($load);

        return view('pages/grafik/judment', $load);
    }

    public function hydrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 3;
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' ' . $station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $select = "station_id, station, station_name,water_level_date, ";
        $group = '';
        $select .= "water_level_time as wt ,water_level_hight as average_wh";
        if ($interval == 10) {
            $waterlevel = WaterLevelModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
                ->where('station_id', $filterStation)
                ->where('water_level_date', $filterDate);
        } elseif ($interval == 30) {
            $waterlevel = WaterLevel30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel_30.station', '=', 'sch_data_station.station_id')
                ->where('station_id', $filterStation)
                ->where('water_level_date', $filterDate);
        } elseif ($interval == 60) {
            $waterlevel = WaterLevel60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel_60.station', '=', 'sch_data_station.station_id')
                ->where('station_id', $filterStation)
                ->where('water_level_date', $filterDate);
        }

        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }

        $data['label'] = [];
        $data['water_level'] = [];
        $data['flow'] = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $data['water_level'][] = doubleval($value['average_wh']);
            $data['label'][] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
        }

        $select = "station_id, station, station_name,flow_date, ";
        $group = '';
        $select .= "flow_time as ft ,flow as average_f";
        if ($interval == 10) {
            $flow = FlowModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                ->where('station_id', $filterStation)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 30) {
            $flow = Flow30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_30.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                ->where('station_id', $filterStation)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 60) {
            $flow = Flow60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_60.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                ->where('station_id', $filterStation)
                //->groupBy('station') 
                ->orderBy('flow_time');
        }


        if ($group) {
            $flow->groupBy(DB::raw($group));
        }


        foreach ($flow->get()->toArray() as $key => $value) {
            //if ($value['flow']) {
            $data['flow'][] = doubleval($value['average_f']);
            //}
        }

        $stationList = StationModel::rightJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where('station_type',  'WL')
            ->orWhere('station_type',  'MF')
            ->groupBy('sch_data_station.station_id')
            ->get()->toArray();

        //dd($data);

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = $stationList;
        $load['data'] = $data;

        return view('pages/grafik/hydrograph', $load);
    }

    public function hytrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Hytrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' ' . $station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        $select .= "rain_fall_time as rt ,rain_fall_continuous as average_rc,rain_fall_1_hour as average_rh";
        if ($interval == 10) {
            $rainfall = RainfallModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                // ->where('station', $filterStation)
                //->groupBy('station') 
                ->orderBy('rain_fall_time');
        } elseif ($interval == 30) {
            $rainfall = Rainfall30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_30.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                // ->where('station', $filterStation)
                //->groupBy('station') 
                ->orderBy('rain_fall_time');
        } elseif ($interval == 60) {
            $rainfall = Rainfall60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_60.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                // ->where('station', $filterStation)
                //->groupBy('station') 
                ->orderBy('rain_fall_time');
        }



        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }

        $data['label'] = [];
        $data['rh'] = [];
        $data['rc'] = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            //$data['rh'][] = doubleval($value['average_rh']);
            $data['datas'][$value['station_id']]['station'] = ($value['station_name']);
            $data['datas'][$value['station_id']]['value'][] = doubleval($value['average_rc']);

            $data['label'][] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
        }

        $stationList = StationModel::rightJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where('station_type',  'RG')
            ->get()->toArray();

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = $stationList;
        $load['data'] = $data;


        return view('pages/grafik/hytrograph', $load);
    }
}
