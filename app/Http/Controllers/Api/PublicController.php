<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\CurentRainfallModel;
use App\Models\Rainfall30Model;
use App\Models\Rainfall60Model;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevel30Model;
use App\Models\WaterLevel60Model;
use App\Models\WaterLevelModel;
use App\Models\WireVibration30Model;
use App\Models\WireVibration60Model;
use App\Models\WireVibrationModel;
use App\Models\Flow30Model;
use App\Models\Flow60Model;
use App\Models\FlowModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends BaseController
{
    public function stationList(Request $request)
    {

        $title = "Station List";

        $station = StationModel::selectRaw('sch_data_station.*,sch_station_types.id,sch_station_types.station_type,alert_column,alert_value')
            ->leftJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id');
        if ($request->station_type) {
            $station->where('station_type', $request->station_type);
        }
        if ($request->station_name) {
            $station->where('station_name', 'LIKE', '%' . $request->station_name . '%');
        }

        $datas = [];
        $stationData = $station->get();
        if (!$stationData->isEmpty()) {
            $title =  $title . ' data found';
            foreach ($stationData as $key => $value) {
                $datas[$value['station_id']]['station_id'] = $value->station_id;
                $datas[$value['station_id']]['station_name'] = $value->station_name;
                $datas[$value['station_id']]['station_lat'] = doubleval('-' . $this->dms_to_dec($value->station_lat));
                $datas[$value['station_id']]['station_long'] = $this->dms_to_dec($value->station_long);
                $datas[$value['station_id']]['station_river'] = $value->station_river;
                $datas[$value['station_id']]['station_prod_year'] = $value->station_prod_year;
                $datas[$value['station_id']]['station_instalaton_date'] = $value->station_instalaton_date;
                $datas[$value['station_id']]['station_authority'] = $value->station_authority;
                $datas[$value['station_id']]['station_guardsman'] = $value->station_guardsman;
                $datas[$value['station_id']]['station_reg_number'] = $value->station_reg_number;
                $datas[$value['station_id']]['station_equipment'][$key] = $value->station_type;
            }
            return $this->sendResponse($datas, $title);
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }


    public function curentRainfall()
    {

        $title = "Curent Rainfall";
        $curetnRainfall = CurentRainfallModel::get();

        if (!$curetnRainfall->isEmpty()) {
            $title =  $title . ' data found';
            return $this->sendResponse($curetnRainfall, $title);
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }

    public function rainfallByStation(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 2;
        $interval = $request->has('interval') ? $request->get('interval') : 60;

        $title = "Rainfall";

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
        //dd($rainfall->get()->toArray());
        $rainfallData = $rainfall->get();

        if (!$rainfallData->isEmpty()) {
            $susunData = [];
            $arrDataByIndex = [];


            foreach ($rainfallData as $key => $value) {
                $susunData[$key]['rain_fall_date'] = Carbon::parse($value['rain_fall_date'])->isoFormat('D MMMM YYYY');
                $susunData[$key] = $value;
                $susunData[$key]['rt'] = Carbon::parse($value['rt'])->isoFormat('HH:mm');

                $arrDataByIndex['rain_fall_10_minut'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_10_minut'];
                $arrDataByIndex['rain_fall_30_minute'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_30_minute'];
                $arrDataByIndex['rain_fall_1_hour'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_1_hour'];
                $arrDataByIndex['rain_fall_3_hour'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_3_hour'];
                $arrDataByIndex['rain_fall_6_hour'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_6_hour'];
                $arrDataByIndex['rain_fall_12_hour'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_12_hour'];
                $arrDataByIndex['rain_fall_24_hour'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_24_hour'];
                $arrDataByIndex['rain_fall_continuous'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_continuous'];
                $arrDataByIndex['rain_fall_effective'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_effective'];
                $arrDataByIndex['rain_fall_effective_intensity'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_effective_intensity'];
                $arrDataByIndex['rain_fall_prev_working'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_prev_working'];
                $arrDataByIndex['rain_fall_working'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_working'];
                $arrDataByIndex['rain_fall_working_24'][Carbon::parse($value['rt'])->isoFormat('HH:mm')] = $value['rain_fall_working_24'];
            }

            $summaryData = [];
            foreach ($arrDataByIndex as $key => $value) {
                $summaryData[$key]['average'] = round(array_sum($value) / count($value), 3);
                $summaryData[$key]['max'] = max($value);
                $summaryData[$key]['time'] = array_search(max($value), $value);
            }
            # code...
            $title = $title . ' ' . $susunData[0]['station_name'] . ' ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['interval'] = $interval . ' Minutes';
            $load['date'] = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['station'] = StationModel::find($filterStation);
            $load['rainfall'] = $susunData;
            $load['summray'] = $summaryData;
            return $this->sendResponse($load, $title);
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }

    public function rainfallDaily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';

        $title = 'Rainfall Daily Report '. Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;


        $select = "station_id, station, station_name,rain_fall_date, ";
        $group = '';

        $select .= "rain_fall_time as rt ,rain_fall_continuous as average_rc,rain_fall_1_hour as average_rh";


        $rainfall = RainfallModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
            ->where('rain_fall_date', $filterDate);

        if ($interval == 60) {
            $rainfall = Rainfall60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_rainfall_60.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', $filterDate);
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

        $rainfallData = $rainfall->get();

        if (!$rainfallData->isEmpty()) {
            $susunData = [];
            $susunGrafik = [];
            $arrDataByStation = [];
            foreach ($rainfallData as $key => $value) {

                $susunData[$value['station']]['station_id'] = $value['station'];
                $susunData[$value['station']]['station_name'] = $value['station_name'];
                $susunData[$value['station']]['rain_fall_date'] = $value['rain_fall_date'];
                $susunData[$value['station']]['datas'][$value['rt']]['rain_fall_time'] = $value['rt'];
                $susunData[$value['station']]['datas'][$value['rt']]['rain_hourly'] = $value['average_rh'];
                $susunData[$value['station']]['datas'][$value['rt']]['rain_continously'] = $value['average_rc'];

                $arrDataByStation[$value['station']]['rh'][$value['rt']] =  $value['average_rh'];
                $arrDataByStation[$value['station']]['rc'][$value['rt']] =  $value['average_rc'];

               
            }
           
            $avergaeRh = [];
            $avergaeRc = [];
            $maxRh = [];
            $maxRc = [];
            $timeRh = [];
            $timeRc = [];
            
            if (isset(($susunData['station']))) {
                foreach ($susunData['station'] as $key => $value) {

                    $avergaeRh[$key]['station_id'] = $value['station_id'];
                    $avergaeRh[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]['rh']) / count($arrDataByStation[$value['station_id']]['rh']), 3);
                    $avergaeRc[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]['rc']) / count($arrDataByStation[$value['station_id']]['rc']), 3);

                    $maxRh[$key]['station_id'] = $value['station_id'];
                    $maxRh[$key]['value'] = max($arrDataByStation[$value['station_id']]['rh']);

                    $maxRc[$key]['station_id'] = $value['station_id'];
                    $maxRc[$key]['value'] = max($arrDataByStation[$value['station_id']]['rc']);

                    $timeRc[$key]['station_id'] = $value['station_id'];
                    $timeRh[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]['rh']), $arrDataByStation[$value['station_id']]['rh']);
                    $timeRc[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]['rc']), $arrDataByStation[$value['station_id']]['rc']);

                }
            }


            $summaryData['average']['rain_hourly'] = $avergaeRh;
            $summaryData['average']['rain_continously'] = $avergaeRc;
            $summaryData['max']['rrain_hourly'] = $maxRh;
            $summaryData['max']['rain_continously'] = $maxRc;
            $summaryData['time']['rain_hourly'] = $timeRh;
            $summaryData['time']['rain_continously'] = $timeRc;

            $load['interval'] = $interval . ' Minutes';
            $load['date'] = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['rainfall'] = array_values($susunData);
            $load['summray'] = $summaryData;
        
            return $this->sendResponse($load, $title);
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }

    public function waterLevel(Request $request){
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Water Level Daily Report '.Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');

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

        //->groupBy(DB::raw()

        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }

        $waterlevelData = $waterlevel->get();

        if (!$waterlevelData->isEmpty()) {
            $susunData = [];
            $arrDataByStation = []; 

            foreach ($waterlevelData as $key => $value) {
                
                $susunData[$value['station_id']]['station_id'] = $value['station_id'];
                $susunData[$value['station_id']]['station_name'] = $value['station_name']; 
                $susunData[$value['station_id']]['water_level_date'] = $value['water_level_date'];
                $susunData[$value['station_id']]['datas'][$value['wt']]['water_level_time'] = $value['wt'];
                $susunData[$value['station_id']]['datas'][$value['wt']]['water_level'] = $value['average_wh'];
                

                $arrDataByStation[$value['station']][$value['wt']] =  $value['average_wh'];
                
            }
    
            $avergae = [];
            $max = [];
            $time = [];
            //dd($susunData);

            if (isset(($susunData))) {
                foreach ($susunData as $key => $value) {
                    //dd($arrDataByStation[$value['station_id']]);
                    $avergae[$key]['station_id'] = $value['station_id'];
                    $avergae[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]) / count($arrDataByStation[$value['station_id']]), 3);

                    $max[$key]['station_id'] = $value['station_id'];
                    $max[$key]['value'] = max($arrDataByStation[$value['station_id']]);

                    $time[$key]['station_id'] = $value['station_id'];
                    $time[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]), $arrDataByStation[$value['station_id']]);
                }
            }
            //dd($susunGrafik);
    
            $summaryData['average'] = $avergae;
            $summaryData['max'] = $max;
            $summaryData['time'] = $time;

            $load['interval'] = $interval . ' Minutes';
            $load['date'] = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['water_level'] = array_values($susunData);
            $load['summray'] = $summaryData;
        
            return $this->sendResponse($load, $title);
    
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }

    public function wireVibration(Request $request){

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Wire & Vibration Daily Report '.Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;
        
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

        if ($group) {
            $wireVibration->groupBy(DB::raw($group));
        }

        $wireVibrationData = $wireVibration->get();
        if (!$wireVibrationData->isEmpty()) {
            $susunData = [];
            $arrDataByStation = []; 

            foreach ($wireVibrationData as $key => $value) {
                
                $susunData[$value['station_id']]['station_id'] = $value['station_id'];
                $susunData[$value['station_id']]['station_name'] = $value['station_name']; 
                $susunData[$value['station_id']]['wire_vibration_date'] = $value['wire_vibration_date'];
                $susunData[$value['station_id']]['datas'][$value['wvt']]['wire_vibration_time'] = $value['wvt'];
                $susunData[$value['station_id']]['datas'][$value['wvt']]['wire'] = $value['average_w'];
                $susunData[$value['station_id']]['datas'][$value['wvt']]['vibration'] = $value['average_v'];
                

                $arrDataByStation[$value['station']]['rh'][$value['wvt']] =  $value['average_w'];
                $arrDataByStation[$value['station']]['rc'][$value['wvt']] =  $value['average_v'];
                
            }

            $avergaeRh = [];
            $avergaeRc = [];
            $maxRh = [];
            $maxRc = [];
            $timeRh = [];
            $timeRc = [];
            
            if (isset(($susunData))) {
                foreach ($susunData as $key => $value) {

                    $avergaeRh[$key]['station_id'] = $value['station_id'];
                    $avergaeRh[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]['rh']) / count($arrDataByStation[$value['station_id']]['rh']), 3);
                    $avergaeRc[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]['rc']) / count($arrDataByStation[$value['station_id']]['rc']), 3);

                    $maxRh[$key]['station_id'] = $value['station_id'];
                    $maxRh[$key]['value'] = max($arrDataByStation[$value['station_id']]['rh']);

                    $maxRc[$key]['station_id'] = $value['station_id'];
                    $maxRc[$key]['value'] = max($arrDataByStation[$value['station_id']]['rc']);

                    $timeRc[$key]['station_id'] = $value['station_id'];
                    $timeRh[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]['rh']), $arrDataByStation[$value['station_id']]['rh']);
                    $timeRc[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]['rc']), $arrDataByStation[$value['station_id']]['rc']);

                }
            }


            $summaryData['average']['wire'] = $avergaeRh;
            $summaryData['average']['vibration'] = $avergaeRc;
            $summaryData['max']['wire'] = $maxRh;
            $summaryData['max']['vibration'] = $maxRc;
            $summaryData['time']['wire'] = $timeRh;
            $summaryData['time']['vibration'] = $timeRc;
    
            $load['interval'] = $interval . ' Minutes';
            $load['date'] = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['wire_vibration'] = array_values($susunData);
            $load['summray'] = $summaryData;
        
            return $this->sendResponse($load, $title);
    
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }

    }

    public function flow(Request $request){
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Flow Daily Report '.Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;
        

        $select = "station_id, station, station_name,flow_date, ";
        $group = '';
        $select .= "flow_time as ft ,flow as average_f";

        if ($interval == 10) {
            $flow = FlowModel::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 30) {
            $flow = Flow30Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_30.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        } elseif ($interval == 60) {
            $flow = Flow60Model::select(DB::raw($select))
                ->leftJoin('sch_data_station', 'sch_data_flow_60.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', $filterDate)
                //->groupBy('station') 
                ->orderBy('flow_time');
        }
 

        if ($group) {
            $flow->groupBy(DB::raw($group));
        }


        $flowData = $flow->get();
        if (!$flowData->isEmpty()) {
            $susunData = [];
            $arrDataByStation = []; 

            foreach ($flowData as $key => $value) {
                
                $susunData[$value['station_id']]['station_id'] = $value['station_id'];
                $susunData[$value['station_id']]['station_name'] = $value['station_name']; 
                $susunData[$value['station_id']]['flow_date'] = $value['flow_date'];
                $susunData[$value['station_id']]['datas'][$value['ft']]['flow_time'] = $value['ft'];
                $susunData[$value['station_id']]['datas'][$value['ft']]['flow'] = $value['average_f'];

                $arrDataByStation[$value['station']][$value['ft']] =  $value['average_f'];
            }
    
            $avergae = [];
            $max = [];
            $time = [];
            //dd($susunData);

            if (isset(($susunData))) {
                foreach ($susunData as $key => $value) {
                    //dd($arrDataByStation[$value['station_id']]);
                    $avergae[$key]['station_id'] = $value['station_id'];
                    $avergae[$key]['value'] = round(array_sum($arrDataByStation[$value['station_id']]) / count($arrDataByStation[$value['station_id']]), 3);

                    $max[$key]['station_id'] = $value['station_id'];
                    $max[$key]['value'] = max($arrDataByStation[$value['station_id']]);

                    $time[$key]['station_id'] = $value['station_id'];
                    $time[$key]['value'] = array_search(max($arrDataByStation[$value['station_id']]), $arrDataByStation[$value['station_id']]);
                }
            }
            //dd($susunGrafik);
    
            $summaryData['average'] = $avergae;
            $summaryData['max'] = $max;
            $summaryData['time'] = $time;
            $load['interval'] = $interval . ' Minutes';
            $load['date'] = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');
            $load['water_level'] = array_values($susunData);
            $load['summray'] = $summaryData;
        
            return $this->sendResponse($load, $title);
    
        } else {
            $title =  $title . ' data not found';
            return $this->sendError($title);
        }
    }

    private function dms_to_dec($dms)
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
}
