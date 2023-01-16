<?php

namespace App\Http\Controllers;

use App\Models\Flow30Model;
use App\Models\Flow60Model;
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
 

        if ($group) {
            $waterlevel->groupBy(DB::raw($group));
        }

        $susunData = [];

        $arrDataByStation = [];
        $susunGrafik = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];

            $susunData['data'][$value['ft']]['date_time'] = Carbon::parse($value['ft'])->isoFormat('HH:mm');
            //$susunData['data'][$value['wt']]['date_time'] = $value['wt'];
            $susunData['data'][$value['ft']]['datas'][] = $value;
            $arrDataByStation[$value['station']][$value['ft']] =  $value['average_f'];

            $susunGrafik['label'][$value['ft']] = Carbon::parse($value['ft'])->isoFormat('HH:mm');
            $susunGrafik['datas'][$value['station']]['station'] = $value['station_name'];
            $susunGrafik['datas'][$value['station']]['value'][] = doubleval($value['average_f']);
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
        $load['susunGrafik'] = $susunGrafik;
        $load['filterDate'] = $filterDate;
        $load['filterInterval'] = $interval;

        return view('pages/flow/daily', $load);
    }
}
