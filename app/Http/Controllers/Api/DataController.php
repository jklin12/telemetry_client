<?php

namespace App\Http\Controllers\Api;

use App\Models\CurentRainfallModel;
use App\Models\Flow30Model;
use App\Models\Flow60Model;
use App\Models\FlowModel;
use App\Models\Rainfall30Model;
use App\Models\Rainfall60Model;
use App\Models\RainfallModel;
use App\Models\StationAssets;
use App\Models\StationHistory;
use App\Models\StationModel;
use App\Models\WaterLevel30Model;
use App\Models\WaterLevel60Model;
use App\Models\WaterLevelModel;
use App\Models\WireVibration30Model;
use App\Models\WireVibration60Model;
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

        $station = StationModel::rightJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id');

        if ($request->type) {
            foreach ($request->type as $key => $value) {
                $station->where('station_type', $value);
            }
        }

        $station->groupBy('sch_data_station.station_id');
        //dd($station->get()->toArray());
        //$station = StationModel::paginate(50);

        $datas = [];
        foreach ($station->get() as $key => $value) {
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

    public function stationDetail($id)
    {
        $title = 'Station Detail';
        $subTitle = '';

        $station = StationModel::selectRaw('sch_data_station.*,sch_station_types.id,sch_station_types.station_type,alert_column,alert_value')
            ->leftJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where(DB::raw('sch_data_station.station_id'), $id)
            ->get();

        $stationAsset = StationAssets::where('station', $id)->get();
        $stationHistory = StationHistory::where('station', $id)->orderByDesc('created_at')->get();

        $susunData = [];
        foreach ($station as $key => $value) {
            $susunData['station_id'] = $value->station_id;
            $susunData['station_name'] = $value->station_name;
            $susunData['station_lat'] = $value->station_lat;
            $susunData['station_long'] = $value->station_long;
            $susunData['station_river'] = $value->station_river;
            $susunData['station_prod_year'] = $value->station_prod_year;
            $susunData['station_instalaton_text'] = $value->station_instalaton_text;
            $susunData['station_authority'] = $value->station_authority;
            $susunData['station_reg_number'] = $value->station_reg_number;
            $susunData['station_guardsman'] = $value->station_guardsman;
            if ($value->id) {
                $susunData['station_types'][$value->id]['id'] = $value->id;
                $susunData['station_types'][$value->id]['station_type'] = $value->station_type;
                $susunData['station_types'][$value->id]['alert_column'] = $value->alert_column;
                $susunData['station_types'][$value->id]['alert_value'] = $value->alert_value;
            }
        }

        $susunData2 = [];
        $susunData2['station_id'] = $susunData['station_id'];
        $susunData2['station_name'] = $susunData['station_name'];
        $susunData2['station_lat'] = $susunData['station_lat'];
        $susunData2['station_long'] = $susunData['station_long'];
        $susunData2['station_river'] = $susunData['station_river'];
        $susunData2['station_prod_year'] = $susunData['station_prod_year'];
        $susunData2['station_instalaton_text'] = $susunData['station_instalaton_text'];
        $susunData2['station_authority'] = $susunData['station_authority'];
        $susunData2['station_reg_number'] = $susunData['station_reg_number'];
        $susunData2['station_guardsman'] = $susunData['station_guardsman'];
        $susunData2['station_types'] = array_values($susunData['station_types']);

        $susunAsset = [];
        foreach ($stationAsset as $key => $value) {
            $susunAsset[$key] = $value;
            $value->stations;
        }
        $susunHistory = [];
        foreach ($stationHistory as $key => $value) {
            $susunHistory[$key] = $value;
            $value->stations;
            $value->asset;
        }
        //dd($stationAsset);

        $load['station'] = $susunData2;
        $load['station_asset'] = $susunAsset;
        $load['station_history'] = $stationHistory;

        return $this->sendResponse($load, $title . ' data found');
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
        $select .= "rain_fall_time as rt ,rain_fall_1_hour as average_rc,rain_fall_1_hour as average_rh";

        if ($interval == 60) {
            $rainfall = Rainfall60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_60.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate)
                ->whereRaw("MINUTE(rain_fall_time) = '00'");
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

        //dd($request->all());

        $rainfallData = [];
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
        $select .= "water_level_time as wt ,water_level_hight as average_wh";

        if ($interval == 10) {
            $waterlevel = WaterLevelModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
                ->where('water_level_date', $filterDate);
        } elseif ($interval == 30) {
            $waterlevel = WaterLevel30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel_30.station', '=', 'sch_data_station.station_id')
                ->where('water_level_date', $filterDate);
        } elseif ($interval == 60) {
            $waterlevel = WaterLevel60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel_60.station', '=', 'sch_data_station.station_id')
                ->where('water_level_date', $filterDate);
        }

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
        $select .= "wire_vibration_time as wvt ,wire as average_w,vibration as average_v";

        if ($interval == 10) {

            $wireVibration = WireVibrationModel::select(DB::raw($select))
                ->where('wire_vibration_date', $filterDate)
                ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
                ->orderBy('wire_vibration_time');
        } elseif ($interval == 30) {
            $wireVibration = WireVibration30Model::select(DB::raw($select))
                ->where('wire_vibration_date', $filterDate)
                ->leftJoin('sch_data_station', 'sch_data_wirevibration_30.station', '=', 'sch_data_station.station_id')
                ->orderBy('wire_vibration_time');
        } elseif ($interval == 60) {
            $wireVibration = WireVibration60Model::select(DB::raw($select))
                ->where('wire_vibration_date', $filterDate)
                ->leftJoin('sch_data_station', 'sch_data_wirevibration_60.station', '=', 'sch_data_station.station_id')
                ->orderBy('wire_vibration_time');
        }

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
        $select .= "flow_time as ft ,flow as average_f";
        if ($interval == 10) {
            $waterlevel = FlowModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 30) {
            $waterlevel = Flow30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_30.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 60) {
            $waterlevel = Flow60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_60.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        }


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
        $select .= "rain_fall_time as rt ,rain_fall_1_hour as average_rc,rain_fall_1_hour as average_rh";

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
