<?php

namespace App\Http\Controllers;

use App\DataTables\StationDataTable;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(StationDataTable $dataTable){

        $title = 'Station List';
        $subTitle = '';

        
        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        return $dataTable->render('pages/station/index',$load);
    }
}
