<?php

namespace App\Http\Controllers;

use App\Models\FlowModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlowController extends Controller
{
    public function daily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Flow Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;

        $select = "station_id, station, station_name,flow_date, ";
        $group = '';
        if ($interval == 10) {
            $select .= "flow_time as ft ,flow as average_f";
        } elseif ($interval == 30) {
            $select .= 'DATE_FORMAT(flow_time, "%H:") as hour,IF("30">MINUTE(flow_time), "00", "30") as ft,ROUND(AVG(flow),3) as average_f';
            $group = 'station,CONCAT(
                HOUR(flow_time),
                IF("30">MINUTE(flow_time), "00", "30")
               )';
        } elseif ($interval == 60) {
            $select .= "flow_time as ft,ROUND(AVG(flow),3) as average_f";
            $group = 'station,HOUR(flow_time)';
        }

        $waterlevel = FlowModel::select(DB::raw($select))
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->groupBy('station') 
            ->orderBy('flow_time');

        if ($group) {
            $waterlevel->groupBy(DB::raw($group));
        }

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            if ($interval == 30) {
                $times =  date($value['hour'] . $value['ft']);
                 $susunData['data'][$value['hour'] . $value['ft']]['date_time'] = $times; 
                $susunData['data'][$value['hour'] . $value['ft']]['datas'][] = $value;
                $arrDataByStation[$value['station']][$value['hour'] . $value['ft']] =  $value['average_f'];
            } else {
                $susunData['data'][$value['ft']]['date_time'] = Carbon::parse($value['ft'])->isoFormat('HH:mm');
                //$susunData['data'][$value['wt']]['date_time'] = $value['wt'];
                $susunData['data'][$value['ft']]['datas'][] = $value;
                $arrDataByStation[$value['station']][$value['ft']] =  $value['average_f'];
            }


        }

        //dd($arrDataByStation);

        $avergae = [];
        $max = [];
        $time = [];
        if (isset(($susunData['station']))) {
            foreach ($susunData['station'] as $key => $value) {
                $avergae[$key] = round(array_sum($arrDataByStation[$value['station_id']]) / count($arrDataByStation[$value['station_id']]), 3);
                $max[$key] = max($arrDataByStation[$value['station_id']]);
                $time[$key] = array_search(max($arrDataByStation[$value['station_id']]), $arrDataByStation[$value['station_id']]);
            }
        }
        //print_r($max);die;
        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;
        $summaryData['time'] = $time;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;
        $load['filterInterval'] = $interval;

        return view('pages/flow/daily', $load);
    }
}
