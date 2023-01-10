<?php

namespace App\Http\Controllers;

use App\Models\WireVibrationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WireVibrationController extends Controller
{
    public function daily(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Wire & Vibration Daily Report ';
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

        if ($group) {
            $wireVibration->groupBy(DB::raw($group));
        }
        //dd($wireVibration->get()->toArray(),$select,$filterDate);
        $susunData = [];
        $susunGrafik = [];
        foreach ($wireVibration->get()->toArray() as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            //$susunData['data'][$value['wire_vibration_time']]['date_time'] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH::mm');
            //$susunData['data'][$value['wire_vibration_time']]['datas'][] = $value;
            if ($interval == 30) {
                $times =  date($value['hour'] . ':' . $value['wvt']);
                $susunData['data'][$times]['datas'][] = $value;
                $susunData['data'][$times]['date_time'] = $times;
                $arrDataByStation[$value['station']][$value['hour'] . $value['wvt']] =  $value['average_w'];
                $arrDataByStation[$value['station']][$value['hour'] . $value['wvt']] =  $value['average_v'];

                $susunGrafik['label'][$value['hour'] . $value['wvt']] = $times;
                $susunGrafik['datas'][$value['station']]['station'] = $value['station_name'];
                $susunGrafik['datas'][$value['station']]['value']['average_w'][] = $value['average_w'];
                $susunGrafik['datas'][$value['station']]['value']['average_v'][] = $value['average_v'];
            } else {
                $susunData['data'][$value['wvt']]['date_time'] = Carbon::parse($value['wvt'])->isoFormat('HH:mm');
                $susunData['data'][$value['wvt']]['datas'][] = $value;
                $arrDataByStation[$value['station']][$value['wvt']] =  $value['average_w'];
                $arrDataByStation[$value['station']][$value['wvt']] =  $value['average_v'];

                $susunGrafik['label'][$value['wvt']] = Carbon::parse($value['wvt'])->isoFormat('HH:mm');
                $susunGrafik['datas'][$value['station']]['station'] = $value['station_name'];
                $susunGrafik['datas'][$value['station']]['value']['average_w'][] = $value['average_w'];
                $susunGrafik['datas'][$value['station']]['value']['average_v'][] = $value['average_v'];
            }
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['susunGrafik'] = $susunGrafik;
        $load['filterDate'] = $filterDate;
        $load['filterInterval'] = $interval;

        return view('pages/wire_vibration/daily', $load);
    }
}
