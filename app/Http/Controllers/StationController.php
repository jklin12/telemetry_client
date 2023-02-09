<?php

namespace App\Http\Controllers;

use App\DataTables\StationDataTable;
use App\Models\StationAssets;
use App\Models\StationHistory;
use App\Models\StationModel;
use App\Models\StationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    public function index(StationDataTable $dataTable)
    {

        $title = 'Station List';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $station = StationModel::selectRaw('sch_data_station.*,sch_station_types.id,sch_station_types.station_type,alert_column,alert_value')
            ->leftJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->get();
        /*$query = "INSERT INTO `sch_station_types`(`station_id`, `station_type`, `alert_value`) VALUES";
        foreach ($station as $key => $value) {
            $explode = explode(',',$value->station_equipment);
            foreach ($explode as $ke => $ve) {
                $query .= "('".$value->station_id."','".$ve."','1'),";
            }

            //print_r($explode);
        } */
        //dd($station->toArray());
        $susunData = [];
        foreach ($station as $key => $value) {
            $susunData[$value->station_id]['station_id'] = $value->station_id;
            $susunData[$value->station_id]['station_name'] = $value->station_name;
            $susunData[$value->station_id]['station_lat'] = $value->station_lat;
            $susunData[$value->station_id]['station_long'] = $value->station_long;
            $susunData[$value->station_id]['station_river'] = $value->station_river;
            $susunData[$value->station_id]['station_prod_year'] = $value->station_prod_year;
            $susunData[$value->station_id]['station_instalaton_text'] = $value->station_instalaton_text;
            $susunData[$value->station_id]['station_authority'] = $value->station_authority;
            $susunData[$value->station_id]['station_reg_number'] = $value->station_reg_number;
            $susunData[$value->station_id]['station_guardsman'] = $value->station_guardsman;
            if ($value->id) {
                $susunData[$value->station_id]['station_types'][$value->id]['id'] = $value->id;
                $susunData[$value->station_id]['station_types'][$value->id]['station_type'] = $value->station_type;
                $susunData[$value->station_id]['station_types'][$value->id]['alert_column'] = $value->alert_column;
                $susunData[$value->station_id]['station_types'][$value->id]['alert_value'] = $value->alert_value;
            }
        }

        $load['datas'] = $susunData;
        //$load['links'] = $station->links();
        $load['arrfield'] = $this->arrfield();

        return view('pages/station/index', $load);
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

    public function formType(Request $request, $stationId, $typeId = 0)
    {
        $title =  'Tambah Data Type';
        $subTitle = '';
        $action = 'addData';

        $data = [];
        if ($typeId) {
            $title = 'Edit Data Type';
            $action = 'editData';
            $data = StationType::find($typeId)->toArray();
            //dd($data);
        }

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['action'] = $action;
        $load['data'] = $data;
        $load['arrField'] = $this->arrFieldType($stationId);

        return view('pages/station/formtype', $load);
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
        if ($action == 'addData') {
            $addData = StationModel::insert($postVal);
            if ($addData) {
                $request->session()->flash('success', 'Add Station Suksess');
                return redirect(route('station.index'));
            } else {
                $request->session()->flash('erorr', 'Add Station Gagal');
                return redirect()->back();
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
            } else {
                $request->session()->flash('erorr', 'Edit Station Gagal');
                return redirect()->back();
            }
        }
        //dd($postVal);
    }
    public function storeType(Request $request)
    {

        $postVal = [];
        $action = $request->input('action');
        foreach ($request->all()['data'] as $key => $value) {
            if ($value) {
                $postVal[$key] = $value;
            }
        }
        if ($action == 'addData') {
            $addData = StationType::insert($postVal);
            if ($addData) {
                $request->session()->flash('success', 'Add Station Equipment Suksess');
                return redirect(route('station.index'));
            } else {
                $request->session()->flash('erorr', 'Add Station Equipment Gagal');
                return redirect()->back();
            }
        }
        if ($action == 'editData') {
            $stationId = $request->input('station_id');

            $editData = StationType::where('station_id', $stationId)
                ->update($postVal);
            //dd($editData);
            if ($editData) {
                $request->session()->flash('success', 'Edit Station Equipment Suksess');
                return redirect(route('station.index'));
            } else {
                $request->session()->flash('erorr', 'Edit Station Equipment Gagal');
                return redirect()->back();
            }
        }
        //dd($postVal);
    }

    public function show($id)
    {
        $title = 'Station Detail';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $station = StationModel::selectRaw('sch_data_station.*,sch_station_types.id,sch_station_types.station_type,alert_column,alert_value')
            ->leftJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where(DB::raw('sch_data_station.station_id'),$id)
            ->get();

        $stationAsset = StationAssets::where('station',$id)->get();
        $stationHistory = StationHistory::where('station',$id)->get();

        $susunData = [];
        foreach ($station as $key => $value) {
            $susunData['station_id'] = $value->station_id;
            $susunData['station_name'] = $value->station_name;
            $susunData['station_lat'] = $value->station_lat;
            $susunData['station_long'] = $value->station_long;
            $susunData['station_river'] = $value->station_river;
            $susunData['station_prod_year'] = $value->station_prod_year;
            $susunData['station_instalaton_text'] = $value->station_instalaton_text;
            $susunData['station_authority'] = $value->station_authority;
            $susunData['station_reg_number'] = $value->station_reg_number;
            $susunData['station_guardsman'] = $value->station_guardsman;
            if ($value->id) {
                $susunData['station_types'][$value->id]['id'] = $value->id;
                $susunData['station_types'][$value->id]['station_type'] = $value->station_type;
                $susunData['station_types'][$value->id]['alert_column'] = $value->alert_column;
                $susunData['station_types'][$value->id]['alert_value'] = $value->alert_value;
            }
        }

        //dd($stationAsset);

        $load['station'] = $susunData;
        $load['station_assets'] = $stationAsset;
        $load['station_history'] = $stationHistory;
        
        return view('pages/station/show', $load);
    }

    public function find(Request $request)
    {

        $type = $request->input('type') == 'WV' ? 'MF' : $request->type;


        $station = StationModel::selectRaw('sch_data_station.*,sch_station_types.id,sch_station_types.station_type,alert_column,alert_value')
            ->leftJoin('sch_station_types', 'sch_data_station.station_id', '=', 'sch_station_types.station_id')
            ->where('station_type', $type)
            ->get();
        $element = '<option value=""> Pilih Station </option>';
        foreach ($station as $key => $value) {
            $element .= ' <option value="' . $value->station_id . '">' . $value->station_name . '</option>';
        }

        echo json_encode($element);
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
            /*'station_equipment' => [
                'label' => 'Equipment',
                'orderable' => false,
                'searchable' => true,
                'form_type' => 'text',

            ],*/
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
            /*'station_alert' => [
                'label' => 'Alert Value',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_alert_column' => [
                'label' => 'Alert Column',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],*/
        ];
    }

    protected function arrFieldType($stationId)
    {
        $stationArr = [];
        $station = StationModel::get();
        foreach ($station as $key => $value) {
            $stationArr[$value->station_id] = $value->station_name;
        }
        return [
            'station_id' => [
                'label' => 'Station',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'select',
                'keyvaldata' => $stationArr,
                'valdata' => $stationId ?? ''
            ],
            'station_type' => [
                'label' => 'Equipment',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'alert_value' => [
                'label' => 'Alert Value',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],


        ];
    }
}
