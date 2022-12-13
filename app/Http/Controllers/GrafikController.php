<?php

namespace App\Http\Controllers;

use App\Models\StationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function judment(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Judment Graph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();

        return view('pages/grafik/judment', $load);
    }

    public function hydrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();

        return view('pages/grafik/hydrograph', $load);
    }

    public function hytrograph(Request $request)
    {
        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $filterStation = $request->has('station') ? $request->get('station') : 1;

        $title = 'Hydrograph';
        $subTitle = Carbon::parse($filterDate)->isoFormat('D MMMM YYYY');;;

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;


        $load['filterDate'] = $filterDate;
        $load['filterStation'] = $filterStation;
        $load['station_list'] = StationModel::get()->toArray();

        return view('pages/grafik/hytrograph', $load);
    }
}
