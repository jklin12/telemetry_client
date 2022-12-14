<?php

namespace App\Http\Controllers\Api;

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

class DataController extends BaseController
{
    public function stationList(Request $request)
    {


        $title = "Station List";
        $station = StationModel::paginate(50);

        $datas = [];
        foreach ($station as $key => $value) {
            $datas[$key]['station_id'] = $value->station_id;
            $datas[$key]['station_name'] = $value->station_name;
            $datas[$key]['station_lat'] = doubleval('-' . $this->dms_to_dec($value->station_lat));
            $datas[$key]['station_long'] = $this->dms_to_dec($value->station_long);
            $datas[$key]['station_river'] = $value->station_river;
            $datas[$key]['station_equipment'] = $value->station_equipment;
            $datas[$key]['station_prod_year'] = $value->station_prod_year;
            $datas[$key]['station_instalaton_date'] = $value->station_instalaton_date;
            $datas[$key]['station_authority'] = $value->station_authority;
            $datas[$key]['station_guardsman'] = $value->station_guardsman;
            $datas[$key]['station_reg_number'] = $value->station_reg_number;
        }



        return $this->sendResponse($datas, $title . ' data found');
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



    public function curentRainFall(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');

        $title = 'Current Rainfall';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        //$response = Http::get('http://202.169.224.46:5000/curentRainfall');
        $curetnRainfall = CurentRainfallModel::where('rain_fall_date', $filterDate)->get();

        $load['datas'] = $curetnRainfall;
        $load['date'] = date('Y-m-d');

        return $this->sendResponse($load, $title . ' data found');
    }

    public function rainfallByStation(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;
        $interval = $request->has('interval') ? $request->get('interval') : 60;
        $title = 'Rainfall By Station ';
        $subTitle = 'by station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "rain_fall_time as rt,rain_fall_10_minut,rain_fall_30_minute,rain_fall_1_hour,rain_fall_3_hour,rain_fall_6_hour,rain_fall_12_hour,rain_fall_24_hour,rain_fall_continuous,rain_fall_effective,rain_fall_effective_intensity,rain_fall_prev_working,rain_fall_working,rain_fall_working_24,rain_fall_remarks";
        } elseif ($interval == 30) {
            $select .= 'HOUR(rain_fall_time) as hour,IF("30">MINUTE(rain_fall_time), "00", "30") as rt,ROUND(AVG(rain_fall_10_minut),3) as rain_fall_10_minut,ROUND(AVG(rain_fall_30_minute),3) as rain_fall_30_minute,ROUND(AVG(rain_fall_1_hour),3) as rain_fall_1_hour,ROUND(AVG(rain_fall_3_hour),3) as rain_fall_3_hour,ROUND(AVG(rain_fall_6_hour),3) as rain_fall_6_hour,ROUND(AVG(rain_fall_12_hour),3) as rain_fall_12_hour,ROUND(AVG(rain_fall_24_hour),3) as rain_fall_24_hour,ROUND(AVG(rain_fall_continuous),3) as rain_fall_continuous,ROUND(AVG(rain_fall_effective),3) as rain_fall_effective,ROUND(AVG(rain_fall_effective_intensity),3) as rain_fall_effective_intensity,ROUND(AVG(rain_fall_prev_working),3) as rain_fall_prev_working,ROUND(AVG(rain_fall_working),3) as rain_fall_working,ROUND(AVG(rain_fall_working_24),3) as rain_fall_working_24, rain_fall_remarks';
            $group = 'station,CONCAT(hour,rt)';
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

        $susunData = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            //$susunData[$key] = $value;
            //$susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');;;
            //$susunData[$key]['rain_fall_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');;

            $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');
            if ($interval == 30) {
                $times =  date($value['hour'] . ':' . $value['rt']);
                $susunData[$key] = $value;
                $susunData[$key]['rt'] = $times;
            } else {

                $susunData[$key] = $value;
                $susunData[$key]['rt'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');;
            }
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['rainfall'] = $susunData;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function dailyRainFall(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : 60;
        $title = 'Daily Rainfall All Station';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;

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
            ->where('rain_fall_date', $filterDate);

        if ($group) {
            $rainfall->groupBy(DB::raw($group));
        }


        $susunData = [];
        $stationData = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            $stationData[$value['station']]['station_name'] = $value['station_name'];
            $rainfallData[$value['rt']]['date_time'] = Carbon::parse($value['rt'])->isoFormat('HH::mm');
            $rainfallData[$value['rt']]['datas'][] = $value;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['station'] = array_values($stationData);
        $load['rainfall'] = array_values($rainfallData);

        return $this->sendResponse($load, $title . ' data found');
    }

    public function waterLevel(Request $request, $stationId = '')
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Daily Water Level ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


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
            ->where('water_level_date', $filterDate);
        //->groupBy(DB::raw()

        if ($stationId) {
            $waterlevel->where('station', $stationId);
        }


        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }


        //dd($waterlevel->get()->toArray());
        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $station[$value['station']]['station_id'] = $value['station_id'];
            $station[$value['station']]['station_name'] = $value['station_name'];
            $datasss[$value['wt']]['date_time'] = Carbon::parse($value['wt'])->isoFormat('HH::mm');
            $datasss[$value['wt']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['average_wh'];
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
        $summaryData['average'] = array_values($avergae);
        $summaryData['max'] = array_values($max);

        $susunData['station'] = array_values($station);
        $susunData['datas'] = array_values($datasss);

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
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Daily Wire & Vibration ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;
        $select = "station_id, station, station_name,wire_vibration_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "wire_vibration_time as wvt ,wire as average_w,vibration as average_v";
        } elseif ($interval == 30) {
            $select .= 'HOUR(wire_vibration_time) as hour,IF("30">MINUTE(wire_vibration_time), "00", "30") as wvt,ROUND(AVG(wire),3) as average_w,ROUND(AVG(vibration),3) as average_v';
            $group = 'station,CONCAT(
                hour,wvt
               )';
        } elseif ($interval == 60) {
            $select .= "wire_vibration_time as wvt,ROUND(AVG(wire),3) as average_w,ROUND(AVG(vibration),3) as average_v";
            $group = 'station,HOUR(wire_vibration_time)';
        }


        $wireVibration = WireVibrationModel::select(DB::raw($select))
            ->where('wire_vibration_date', '2022-12-06')
            ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
            ->orderBy('wire_vibration_time');

        if ($request->has('station') && $request->input('station')) {
            $wireVibration->where('station', $request->input('station'));
        }

        if ($group) {
            $wireVibration->groupBy(DB::raw($group));
        }
        //dd($wireVibration->get()->toArray(),$select,$filterDate);
        $susunData = [];
        foreach ($wireVibration->get()->toArray() as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $station[$value['station']]['station_name'] = $value['station_name'];
            $datasss[$value['wvt']]['date_time'] = Carbon::parse($value['wvt'])->isoFormat('HH::mm');
            $datasss[$value['wvt']]['datas'][] = $value;
        }

        $susunData['station'] = array_values($station);
        $susunData['datas'] = array_values($datasss);

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function flow(Request $request, $stationId = '')
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Daily Flow';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


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

        $waterlevel = FlowModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->groupBy('station') 
            ->orderBy('flow_time');

        if ($stationId) {
            $waterlevel->where('station', $stationId);
        }
        if ($group) {
            $waterlevel->groupBy(DB::raw($group));
        }

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {

            $station[$value['station']]['station_id'] = $value['station_id'];
            $station[$value['station']]['station_name'] = $value['station_name'];
            $datasss[$value['ft']]['date_time'] = Carbon::parse($value['ft'])->isoFormat('HH::mm');
            $datasss[$value['ft']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['average_f'];
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
        $summaryData['average'] = array_values($avergae);
        $summaryData['max'] = array_values($max);

        $susunData['station'] = array_values($station);
        $susunData['datas'] = array_values($datasss);


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;

        return $this->sendResponse($load, $title . ' data found');
    }

    public function hydrograph(Request $request)
    {

        $filterDate = $request->has('date') ? $request->post('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->post('station') : 1;
        $interval = $request->has('interval') ? $request->post('interval') : '60';

        //dd($request->all());
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

        $data  = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $data[$key]['wh'] = doubleval($value['average_wh']);
            if ($interval == 30) {
                $data[$key]['time'] = $value['hour'] . ':' . $value['wt'];
            } else {
                $data[$key]['time'] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
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
            $data[$key]['flow'] = doubleval($value['average_f']);
            //}
        }

        //dd($data);

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return $this->sendResponse($load, $title . ' data found');
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

        $data = [];
        foreach ($rainfall->get()->toArray() as $key => $value) {
            $data[$key]['rh'] = doubleval($value['average_rh']);
            $data[$key]['rc'] = doubleval($value['average_rc']);
            if ($interval == 30) {
                $data[$key]['label'] = $value['hour'] . ':' . $value['rt'];
            } else {
                $data[$key]['label'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');
            }
        }

        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['filterInterval'] = $interval;
        $load['station_list'] = StationModel::get()->toArray();
        $load['data'] = $data;

        return $this->sendResponse($load, $title . ' data found');
    }
}
