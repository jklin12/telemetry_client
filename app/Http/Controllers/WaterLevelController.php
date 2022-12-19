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
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Water Level Daily Report ';
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

        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }


        //dd($waterlevel->get()->toArray());
        $susunData = [];

        $arrDataByStation = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            if ($interval == 30) {
                $times =  date($value['hour'] . ':' . $value['wt']);
                $susunData['data'][$value['hour'] . $value['wt']]['date_time'] = $times;

                $susunData['data'][$value['hour'] . $value['wt']]['datas'][] = $value;

                $arrDataByStation[$value['station']][$value['hour'] . $value['wt']] =  $value['average_wh'];
            } else {
                $susunData['data'][$value['wt']]['date_time'] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
                //$susunData['data'][$value['wt']]['date_time'] = $value['wt'];
                $susunData['data'][$value['wt']]['datas'][] = $value;
                $arrDataByStation[$value['station']][$value['wt']] =  $value['average_wh'];
            }
        }

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

        $summaryData['average'] = $avergae;
        $summaryData['max'] = $max;
        $summaryData['time'] = $time;



        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['summaryData'] = $summaryData;
        $load['filterDate'] = $filterDate;
        $load['filterInterval'] = $interval;


        return view('pages/water_level/daily', $load);
    }
}
