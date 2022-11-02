<?php

namespace App\Http\Controllers;

use App\Models\StationModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $title = 'Dashboard';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;



        $station = StationModel::get();

        $susunData = [];
        foreach ($station as $key => $value) {

            $susunData[$key]['type'] = 'Feature';
            $susunData[$key]['properties']['description'] = '<strong>' . $value->station_name . '</strong><p>' . $value->station_station_river . '<br>' . $value->station_equipment . '<br>' . $value->station_authority . '<br>' . $value->station_guardsman . '</p>';
            $susunData[$key]['properties']['icon'] = 'mountain-11';
            //$susunData[$key]['properties']['icon'] = $value->station_icon;
            $susunData[$key]['geometry']['type'] = 'Point';
            $susunData[$key]['geometry']['coordinates'][] = $this->dms_to_dec($value->station_long);
            $susunData[$key]['geometry']['coordinates'][] = doubleval('-' . $this->dms_to_dec($value->station_lat));
        }

        //dd($susunData);
        $load['datas'] = json_encode($susunData);

        return view('pages/dashboard/index', $load);
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
}
