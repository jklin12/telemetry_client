<?php

namespace App\Http\Controllers;

use App\Models\StationModel;
use App\Models\WaterLevel30Model;
use App\Models\WaterLevel60Model;
use App\Models\WaterLevelModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaterLevelController extends Controller
{
    public function notification()
    {
        $datas = WaterLevel60Model::where('water_level_date', date('Y-m-d'))
            ->where('read', 0)
            ->where('water_level_time', '<=', date("H:m"))

            ->leftJoin('sch_data_station', 'sch_data_waterlevel_60.station', '=', 'sch_data_station.station_id')
            ->get();

        $dataId = [];
        foreach ($datas as $key => $value) {
            $dataId[] = $value->water_level_id;

            if ($value->water_level_hight > $value->level_siaga_3) {
                $title = "Status Station " . $value->station_name . " : SIAGA 3";
                $subtitle = "Tinggi Air Saat Ini : " . $value->water_level_hight . ", Tinggi Air Normal : <" . $value->level_siaga_3;

                $this->send($title, $subtitle);
                echo 'notifikasi siaga 3';
            } else  if ($value->water_level_hight > $value->level_siaga_3) {
                $title = "Status Station " . $value->station_name . " : SIAGA 2";
                $subtitle = "Tinggi Air Saat Ini : " . $value->water_level_hight . ", Tinggi Air Normal : <" . $value->level_siaga_2;

                $this->send($title, $subtitle);
                echo 'notifikasi siaga 2';
            } elseif ($value->water_level_hight > $value->level_siaga_1) {
                $title = "Status Station " . $value->station_name . " : SIAGA 1";
                $subtitle = "Tinggi Air Saat Ini : " . $value->water_level_hight . ", Tinggi Air Normal : <" . $value->level_siaga_1;

                $this->send($title, $subtitle);
                echo 'notifikasi siaga 1';
            }
        }
        WaterLevel60Model::whereIn('water_level_id', $dataId)->update(['read' => 1]);
        //dd($dataId);
    }

    public function send($title, $body)
    {

        $customData = [];
        $fields =
            [
                "to" => '/topics/all_user',
                //"to" => 'dvaF11RNS-u-mMGVsU-I7A:APA91bGTZBao-HeDdTVxJelJqqb8FIGF2Mm6cGV3GHb4ckD1MVHBxRr1rgOFK4BbAiqFIYxwjJKiFGoNj80nug6VWnHthAO4qY1sZwJSmEjC0VFTgbpXx92XZYwnkj78iMnGuYDWVFgF',
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                //"data" => $customData
            ];



        $headers = array(
            'Authorization: key=AAAA5SGqijE:APA91bHOxeFlii3Fi1XFxeicb-n5BHKi7ab9euTUSW-eu7OpZemcYggLVAcccECJES6WF6iYgrrkuN1t2c6V0rq7hy15tPZekhXHA6Z4j2CkgRL5w7CMiX4jB_WvkiuzyoRRnneMpAw4',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function daily(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'Water Level Daily Report ';
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

        //->groupBy(DB::raw()

        if ($group) {

            $waterlevel->groupBy(DB::raw($group))
                ->orderBy('water_level_time');
        }


        //dd($waterlevel->get()->toArray());
        $susunData = [];

        $arrDataByStation = [];
        $susunGrafik = [];
        foreach ($waterlevel->get()->toArray() as $key => $value) {
            $susunData['station'][$value['station']]['station_id'] = $value['station_id'];
            $susunData['station'][$value['station']]['station_name'] = $value['station_name'];

            $susunData['data'][$value['wt']]['date_time'] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
            //$susunData['data'][$value['wt']]['date_time'] = $value['wt'];
            $susunData['data'][$value['wt']]['datas'][] = $value;
            $arrDataByStation[$value['station']][$value['wt']] =  $value['average_wh'];

            $susunGrafik['label'][$value['wt']] = Carbon::parse($value['wt'])->isoFormat('HH:mm');
            $susunGrafik['datas'][$value['station']]['station'] = $value['station_name'];
            $susunGrafik['datas'][$value['station']]['value'][] = doubleval($value['average_wh']);
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
        //dd($susunGrafik);

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
        //dd($filterDate);



        return view('pages/water_level/daily', $load);
    }

    public function edit(Request $request)
    {

        $title = 'Edit Data Water Level ';
        $subTitle = '';
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['stations'] = StationModel::get();
        $load['date'] = $request->date;
        $load['station'] = $request->station;

        $time = $this->hoursRange();
        $load['times'] = $time;
        $datas = WaterLevel60Model::where('station', $request->station)
            ->where('water_level_date', $request->date)
            ->get();
        $susunData = [];
        foreach ($datas as $key => $value) {
            $susunData[Carbon::parse($value->water_level_time)->isoFormat('HH:mm')] = $value->water_level_hight;
        }
        $load['data'] = $susunData;

        return view('pages/water_level/edit', $load);
    }
    public function update(Request $request)
    {
        $request->validate([
            'station' => ['required'],
            'water_level_date' => ['required', 'date'],
        ]);

        $postval = [];
        foreach ($request->water_level_hight as $key => $value) {

            if ($key == '00:00') {
                $postval[$key]['station'] = $request->station;
                $postval[$key]['water_level_date'] = $request->water_level_date;
                $postval[$key]['water_level_time'] = '24:00:00';
                $postval[$key]['water_level_hight'] = $value;
            } else {
                $postval[$key]['station'] = $request->station;
                $postval[$key]['water_level_date'] = $request->water_level_date;
                $postval[$key]['water_level_time'] = $key;
                $postval[$key]['water_level_hight'] = $value;
            }
        }
        WaterLevel60Model::where('station', $request->station)
            ->where('water_level_date', $request->water_level_date)
            ->delete();

        //dd($postval);
        WaterLevel60Model::insert($postval);
        $request->session()->flash('success', 'Tambah Users Suksess');
        return redirect()->route('water_level.daily', 'interval=60&date=' . $request->water_level_date);
    }

    public function store(Request $request)
    {

        $request->validate([
            'station' => ['required'],
            'water_level_date' => ['required', 'date'],
        ]);

        $postval = [];
        foreach ($request->water_level_hight as $key => $value) {

            if ($key == '00:00') {
                $postval[$key]['station'] = $request->station;
                $postval[$key]['water_level_date'] = $request->water_level_date;
                $postval[$key]['water_level_time'] = '24:00:00';
                $postval[$key]['water_level_hight'] = $value;
            } else {
                $postval[$key]['station'] = $request->station;
                $postval[$key]['water_level_date'] = $request->water_level_date;
                $postval[$key]['water_level_time'] = $key;
                $postval[$key]['water_level_hight'] = $value;
            }
        }
        //dd($postval);
        WaterLevel60Model::insert($postval);
        $request->session()->flash('success', 'Tambah Users Suksess');
        return redirect()->route('water_level.daily');
    }


    public function create()
    {

        $title = 'Tambah Data Water Level ';
        $subTitle = '';
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['stations'] = StationModel::get();

        $time = $this->hoursRange();
        $load['times'] = $time;
        return view('pages/water_level/create', $load);
    }

    function hoursRange($lower = 0, $upper = 86400, $step = 3600, $format = '')
    {
        $times = array();

        if (empty($format)) {
            $format = 'g:i a';
        }

        foreach (range($lower, $upper, $step) as $increment) {
            $increment = gmdate('H:i', $increment);

            list($hour, $minutes) = explode(':', $increment);

            $date = new DateTime($hour . ':' . $minutes);

            $times[(string) $increment] = $date->format($format);
        }

        return $times;
    }
}
