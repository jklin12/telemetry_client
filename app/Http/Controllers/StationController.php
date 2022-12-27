<?php

namespace App\Http\Controllers;

use App\DataTables\StationDataTable;
use App\Models\StationModel;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(StationDataTable $dataTable)
    {

        $title = 'Station List';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        return $dataTable->render('pages/station/index', $load);
    }

    public function form(Request $request, $station_id = 0)
    {


        $title =  'Tambah Data';
        $subTitle = '';
        $action = 'addData';

        $data = [];
        if ($station_id) {
            $title = 'Edit Data Station';
            $action = 'editData';
            $data = StationModel::find($station_id)->toArray();
            //dd($data);
        }


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['action'] = $action;
        $load['arrField'] = $this->arrField();
        $load['data'] = $data;

        return view('pages/station/form', $load);
    }

    public function store(Request $request)
    {

        $postVal = [];
        $action = $request->input('action');
        foreach ($request->all()['data'] as $key => $value) {
            if ($value) {
                $postVal[$key] = $value;
            }
        }
        if ($action == 'editData') {
            $stationId = $request->input('station_id');
            
            $editData = StationModel::where('station_id', $stationId)
                ->update($postVal);
            //dd($editData);
            if ($editData) {
                $request->session()->flash('success', 'Edit Station Suksess');
                return redirect(route('station.index'));
            }else{
                $request->session()->flash('erorr', 'Edit Station Gagal');
                return redirect()->back();
            }
            
        }
        //dd($postVal);
    }

    protected function arrField()
    {
        return [
            'station_name' => [
                'label' => 'Station Name',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'station_lat' => [
                'label' => 'Latitude',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_long' => [
                'label' => 'Longitude',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_river' => [
                'label' => 'River',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
                //'keyvaldata' => $this->arrStatus
            ],
            'station_equipment' => [
                'label' => 'Equipment',
                'orderable' => false,
                'searchable' => true,
                'form_type' => 'text',

            ],
            'station_prod_year' => [
                'label' => 'Product Year',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
                //'keyvaldata' => $this->arrPiStatus
            ],
            'station_instalaton_text' => [
                'label' => 'Instalation text',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_authority' => [
                'label' => 'Authority',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_guardsman' => [
                'label' => 'Guardsman',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_reg_number' => [
                'label' => 'Register Number',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_alert' => [
                'label' => 'Alert Value',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            /*'station_alert_column' => [
                'label' => 'Alert Column',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],*/
        ];
    }
}
