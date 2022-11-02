<?php

namespace App\Http\Controllers;

use App\Models\WaterLevelModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaterLevelController extends Controller
{
    public function daily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $title = 'Water Level Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $waterlevel = WaterLevelModel::select('station_id', 'station', 'station_name', 'water_level_date', 'water_level_time', 'water_level_hight')
            ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
            ->where('water_level_date', $filterDate)
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('water_level_time')
            ->get()->toArray();

        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['water_level_time']]['date_time'] = Carbon::parse($value['water_level_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['water_level_time']]['datas'][] = $value;

            $arrDataByStation[$value['station']][] =  $value['water_level_hight'];
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
        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;


        return view('pages/water_level/daily', $load);
    }
}
