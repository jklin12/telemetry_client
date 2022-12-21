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
            $select .= "rain_fall_time as rt ,rain_fall_1_hour as average_rc,rain_fall_1_hour as average_rh";
        } elseif ($interval == 30) {
            $select .= 'HOUR(rain_fall_time) as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh';
            $group = 'station,CONCAT(hour,rt)';
        } elseif ($interval == 60) {
            $select .= "rain_fall_time as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh";
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


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/judment', $load);
    }

    public function hydrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' ' . $station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $select = "station_id, station, station_name,water_level_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "water_level_time as wt ,water_level_hight as average_wh";
        } elseif ($interval == 30) {
            $select .= 'HOUR(water_level_time) as hour,IF("30">MINUTE(water_level_time), "00", "30") as wt,ROUND(AVG(water_level_hight),3) as average_wh';
            $group = 'station,CONCAT(hour,wt)';
        } elseif ($interval == 60) {
            $select .= "water_level_time as wt,ROUND(AVG(water_level_hight),3) as average_wh";
            $group = 'station,HOUR(water_level_time)';
        }
        $waterlevel = WaterLevelModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('station_id', $filterStation)
            ->where('water_level_date', $filterDate);

        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }

        $data['label'] = [];
        $data['water_level'] = [];
        $data['flow'] = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $data['water_level'][] = doubleval($value['average_wh']);
            if ($interval == 30) {
                $data['label'][] = $value['hour'] . ':' . $value['wt'];
            } else {
                $data['label'][] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
            }
        }

        $select = "station_id, station, station_name,flow_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "flow_time as ft ,flow as average_f";
        } elseif ($interval == 30) {
            $select .= 'HOUR(flow_time) as hour,IF("30">MINUTE(flow_time), "00", "30") as ft,ROUND(AVG(flow),3) as average_f';
            $group = 'station,CONCAT(hour,ft)';
        } elseif ($interval == 60) {
            $select .= "flow_time as ft,ROUND(AVG(flow),3) as average_f";
            $group = 'station,HOUR(flow_time)';
        }

        $flow = FlowModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            ->where('station_id', $filterStation)
            //->groupBy('station') 
            ->orderBy('flow_time');

        if ($group) {
            $flow->groupBy(DB::raw($group));
        }


        foreach ($flow->get()->toArray() as $key => $value) {
            //if ($value['flow']) {
                $data['flow'][] = doubleval($value['average_f']);
            //}
        }

        //dd($data);

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/hydrograph', $load);
    }

    public function hytrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $station = StationModel::find($filterStation);
        $title .= ' ' . $station->station_name;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "rain_fall_time as rt ,rain_fall_1_hour as average_rc,rain_fall_1_hour as average_rh";
        } elseif ($interval == 30) {
            $select .= 'HOUR(rain_fall_time) as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh';
            $group = 'station,CONCAT(hour,rt)';
        } elseif ($interval == 60) {
            $select .= "rain_fall_time as rt,ROUND(AVG(rain_fall_1_hour),3) as average_rc,ROUND(AVG(rain_fall_1_hour),3) as average_rh";
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

        $data['label'] = [];
        $data['rh'] = [];
        $data['rc'] = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            $data['rh'][] = doubleval($value['average_rh']);
            $data['rc'][] = doubleval($value['average_rc']);
            if ($interval == 30) {
                $data['label'][] = $value['hour'] . ':' . $value['rt'];
            } else {
                $data['label'][] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            }
        }

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return view('pages/grafik/hytrograph', $load);
    }
}
