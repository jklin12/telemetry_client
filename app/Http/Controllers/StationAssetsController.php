<?php

namespace App\Http\Controllers;

use App\Models\StationAssets;
use App\Models\StationModel;
use Illuminate\Http\Request;
use Image;
use File;

class StationAssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Station Assets Data';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrfield'] = $this->arrField();
        $load['datas'] = StationAssets::paginate(5);


        return view('pages/station_assets/index', $load);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title =  'Tambah Assets Satation';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrField'] = $this->arrField();

        return view('pages/station_assets/create', $load);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'station' => ['required', 'integer'],
            'file' => ['mimes:jpg,jpeg,png'],
        ]);

        $postval['station']  = $request->station;
        $postval['asset_name']  = $request->asset_name;
        $postval['asset_brand']  = $request->asset_brand ?? '';
        $postval['asset_type']  = $request->asset_type;
        $postval['asset_serial_number']  = $request->asset_serial_number;
        $postval['asset_spesification']  = $request->asset_spesification;
        $postval['asset_year']  = $request->asset_year;

        $insert = StationAssets::create($postval);

        if ($request->hasFile('file')) {

            $filenamewithextension = $request->file('file')->getClientOriginalName();

            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = time() . '.' . $extension;
            $mediumthumbnail = 'medium_' . time() . '.' . $extension;

            $request->file('file')->storeAs('public/station_assets', $filenametostore);
            $request->file('file')->storeAs('public/station_assets/thumbnail', $mediumthumbnail);

            $mediumthumbnailpath = public_path('storage/station_assets/thumbnail/' . $mediumthumbnail);
            $this->createThumbnail($mediumthumbnailpath, 150, 93);

            $updateVal['asset_tumbnial'] = 'storage/station_assets/thumbnail/' . $mediumthumbnail;
            $updateVal['asset_imgae'] = 'storage/station_assets/' . $filenametostore;

            StationAssets::find($insert->assets_id)->update($updateVal);
        }

        return redirect()->route('station_assets.index')->with('success', 'Tambah Data Assetes Suksess');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StationAssets  $stationAssets
     * @return \Illuminate\Http\Response
     */
    public function show(StationAssets $stationAssets)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StationAssets  $stationAssets
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stationAsset = StationAssets::find($id);

        $title =  'Edit Station Assets';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['data'] = $stationAsset;
        $load['arrField'] = $this->arrField($id);

        return view('pages/station_assets/edit', $load);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StationAssets  $stationAssets
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'station' => ['required', 'integer'],
            'file' => ['mimes:jpg,jpeg,png'],
        ]);

        $postval['station']  = $request->station;
        $postval['asset_name']  = $request->asset_name;
        $postval['asset_brand']  = $request->asset_brand ?? '';
        $postval['asset_type']  = $request->asset_type;
        $postval['asset_serial_number']  = $request->asset_serial_number;
        $postval['asset_spesification']  = $request->asset_spesification;
        $postval['asset_year']  = $request->asset_year;

         StationAssets::find($id)->update($postval);

        if ($request->hasFile('file')) {

            $filenamewithextension = $request->file('file')->getClientOriginalName();

            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = time() . '.' . $extension;
            $mediumthumbnail = 'medium_' . time() . '.' . $extension;

            $request->file('file')->storeAs('public/station_assets', $filenametostore);
            $request->file('file')->storeAs('public/station_assets/thumbnail', $mediumthumbnail);

            $mediumthumbnailpath = public_path('storage/station_assets/thumbnail/' . $mediumthumbnail);
            $this->createThumbnail($mediumthumbnailpath, 150, 93);

            $updateVal['asset_tumbnial'] = 'storage/station_assets/thumbnail/' . $mediumthumbnail;
            $updateVal['asset_imgae'] = 'storage/station_assets/' . $filenametostore;

            StationAssets::find($id)->update($updateVal);
        }
        return redirect()->route('station_assets.index')->with('success', 'Edit Data Assets Suksess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StationAssets  $stationAssets
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stationAssets = StationAssets::find($id);
        File::delete($stationAssets->asset_tumbnial);
        File::delete($stationAssets->asset_imgae);
        $stationAssets->delete();
        return redirect()->route('station_assets.index')->with('success', 'Hapus Data Aseets Suksess');
    }

    public function ajax($id){
        $stationAsset = StationAssets::where('station',$id)->get();
        echo json_encode($stationAsset);
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    public function arrfield()
    {
        $station = StationModel::get();
        $arrStation = [];
        foreach ($station as $key => $value) {
            $arrStation[$value->station_id] = $value->station_name;
        }
        return [
            'station' => [
                'label' => 'Station',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'select2',
                'keyvaldata' => $arrStation
            ],
            'asset_name' => [
                'label' => 'Nama Asset',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'asset_brand' => [
                'label' => 'Merek',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'asset_type' => [
                'label' => 'Tipe',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'asset_serial_number' => [
                'label' => 'Serial Number',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'asset_spesification' => [
                'label' => 'Spesifikasi',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'area',
            ],
            'asset_year' => [
                'label' => 'Tahun Pengadaan',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'year',
            ],
            'file' => [
                'label' => 'Foto',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'file',
            ],
        ];
    }
}
