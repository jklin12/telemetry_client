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
        $title = 'Water Level Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $waterlevel = FlowModel::select('station_id', 'station', 'station_name', 'flow_date', 'flow_time', 'flow')
            ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
            ->where('flow_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('flow_time')
            ->get()->toArray();

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['flow_time']]['date_time'] = Carbon::parse($value['flow_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['flow_time']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['flow'];
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
        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;


        return view('pages/flow/daily', $load);
    }
}
