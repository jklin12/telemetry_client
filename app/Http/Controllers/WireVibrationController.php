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
        $title = 'Wire & Vibration Daily Report ';
        $subTitle = 'All Station ' . Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;


        $wireVibration = WireVibrationModel::select(
            'station_id',
            'station',
            'station_name',
            'wire_vibration_date',
            'wire_vibration_time',
            'wire',
            'vibration',
        )->where('wire_vibration_date', $filterDate)
            ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
            //->groupBy('station')
            ->orderBy(DB::raw('sch_data_station.station_id'))
            ->orderBy('wire_vibration_time')
            ->get()->toArray();

        

        $susunData = []; 

        foreach ($wireVibration as $key => $value) {
            //$susunData[$value['date_time']]['date_time'] = Carbon::parse($value['rain_fall_time'])->isoFormat('HH::mm');
            //$susunData[$value['date_time']]['station_name'] = $value['station_name'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];
            $susunData['data'][$value['wire_vibration_time']]['date_time'] = Carbon::parse($value['wire_vibration_time'])->isoFormat('HH::mm');
            $susunData['data'][$value['wire_vibration_time']]['datas'][] = $value;
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['datas'] = $susunData;
        $load['filterDate'] = $filterDate;

        return view('pages/wire_vibration/daily', $load);
    }
}
