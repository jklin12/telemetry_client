<?php

namespace App\Http\Controllers;

use App\Models\StationHistory;
use App\Models\StationModel;
use Illuminate\Http\Request;
use Image;
use File;

class StationHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $title = 'Station Maintanance Data';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrfield'] = $this->arrField();
        $load['datas'] = StationHistory::paginate(10);

        
        return view('pages/station_history/index', $load);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title =  'Tambah Data Maintanance';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrField'] = $this->arrField();

        return view('pages/station_history/create', $load);
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
            'station' => ['required', 'integer'],
            'history_title' => ['required', 'string', 'max:255'],
            'file' => ['mimes:jpg,jpeg,png'],
        ]);

        $postval['station']  = $request->station;
        $postval['assets']  = $request->assets;
        $postval['history_title']  = $request->history_title;
        $postval['history_body']  = $request->history_body;

        $insert = StationHistory::create($postval);

        if ($request->hasFile('file')) {

            $filenamewithextension = $request->file('file')->getClientOriginalName();

            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = time() . '.' . $extension;
            $mediumthumbnail = 'medium_' . time() . '.' . $extension;

            $request->file('file')->storeAs('public/station_history', $filenametostore);
            $request->file('file')->storeAs('public/station_history/thumbnail', $mediumthumbnail);

            $mediumthumbnailpath = public_path('storage/station_history/thumbnail/' . $mediumthumbnail);
            $this->createThumbnail($mediumthumbnailpath, 150, 93);

            $updateVal['history_tumbnial'] = 'storage/station_history/thumbnail/' . $mediumthumbnail;
            $updateVal['history_imgae'] = 'storage/station_history/' . $filenametostore;

            StationHistory::find($insert->history_id)->update($updateVal);
        }
        return redirect()->route('station_history.index')->with('success', 'Add Station Maintanance Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StationHistory  $stationHistory
     * @return \Illuminate\Http\Response
     */
    public function show(StationHistory $stationHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StationHistory  $stationHistory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stationAsset = StationHistory::find($id);

        $title =  'Edit Station Maintanance';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['data'] = $stationAsset;
        $load['arrField'] = $this->arrField($id);

        return view('pages/station_history/edit', $load);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StationHistory  $stationHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'station' => ['required', 'integer'],
            'history_title' => ['required', 'string', 'max:255'],
            'file' => ['mimes:jpg,jpeg,png'],
        ]);

        $postval['station']  = $request->station;
        $postval['assets']  = $request->assets;
        $postval['history_title']  = $request->history_title;
        $postval['history_body']  = $request->history_body;

        $insert = StationHistory::find($id)->update($postval);

        if ($request->hasFile('file')) {

            $filenamewithextension = $request->file('file')->getClientOriginalName();

            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = time() . '.' . $extension;
            $mediumthumbnail = 'medium_' . time() . '.' . $extension;

            $request->file('file')->storeAs('public/station_history', $filenametostore);
            $request->file('file')->storeAs('public/station_history/thumbnail', $mediumthumbnail);

            $mediumthumbnailpath = public_path('storage/station_history/thumbnail/' . $mediumthumbnail);
            $this->createThumbnail($mediumthumbnailpath, 150, 93);

            $updateVal['history_tumbnial'] = 'storage/station_history/thumbnail/' . $mediumthumbnail;
            $updateVal['history_imgae'] = 'storage/station_history/' . $filenametostore;

            StationHistory::find($id)->update($updateVal);
        }
        return redirect()->route('station_history.index')->with('success', 'Edit Station Maintanance Sucess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StationHistory  $stationHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stationAssets = StationHistory::find($id);
        File::delete($stationAssets->asset_tumbnial);
        File::delete($stationAssets->asset_imgae);
        $stationAssets->delete();
        return redirect()->route('station_history.index')->with('success', 'Hapus Data Aseets Suksess');
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
            'assets' => [
                'label' => 'Assets',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'select2',
                'keyvaldata' => []
            ],
            'history_title' => [
                'label' => 'Judul',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'history_body' => [
                'label' => 'Isi',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'area',
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
