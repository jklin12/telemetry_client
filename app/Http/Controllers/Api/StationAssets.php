<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\StationAssets as ModelsStationAssets;
use Illuminate\Http\Request;
use File;
use Image;

class StationAssets extends BaseController
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


        $stationAssets = ModelsStationAssets::get();

        foreach ($stationAssets as $key => $value) {
            $value->stations;
        }

        $stationAssets;


        return $this->sendResponse($stationAssets, $title . ' data found');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = 'Tambah Assets Data';
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

        $insert = ModelsStationAssets::create($postval);

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

            ModelsStationAssets::find($insert->assets_id)->update($updateVal);
        }

        return $this->sendResponse($insert, $title . ' Sukses');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $title = 'Detail Assets Data';
        $subTitle = '';

        $stationAssets = ModelsStationAssets::find($id);
        $stationAssets->stations ?? null;

        return $this->sendResponse($stationAssets, $title . ' data found');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $title = 'Edit Assets Data';
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

        $update = ModelsStationAssets::find($id)->update($postval);

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

            ModelsStationAssets::find($id)->update($updateVal);
        }

        return $this->sendResponse($update, $title . ' Sukses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $title = 'Station Assets Data';

        $stationAssets  = ModelsStationAssets::find($id);
        File::delete($stationAssets->asset_tumbnial);
        File::delete($stationAssets->asset_imgae);
        $stationAssets->delete();

        return $this->sendResponse($stationAssets, 'Hapus ' . $title . ' Sukses');
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}
