<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Models\StationHistory as ModelsStationHistory;
use Illuminate\Http\Request;
use File;
use Image;

class StationHistory extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Riwayat Perawatan';
	//dd(auth()->user()->id);
        $stationHistory = ModelsStationHistory::where('created_by',auth()->user()->id)->orderByDesc('created_at')->get();

        foreach ($stationHistory as $key => $value) {
            $value->stations;
            $value->asset;
        }
        $stationHistory;

        return $this->sendResponse($stationHistory, $title . ' data found');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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

        $insert = ModelsStationHistory::create($postval);

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

            ModelsStationHistory::find($insert->history_id)->update($updateVal);
        }

        return $this->sendResponse($insert, 'Tambah Perawatan Sukses');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Riwayat Perawatan';

        $stationHistory = ModelsStationHistory::find($id);

        $stationHistory->stations ?? null;
        $stationHistory->asset ?? null;

        return $this->sendResponse($stationHistory, $title . ' data found');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $request->validate([
            'station' => ['required', 'integer'],
            'history_title' => ['required', 'string', 'max:255'],
            'file' => ['mimes:jpg,jpeg,png'],
        ]);

        $postval['station']  = $request->station;
        $postval['assets']  = $request->assets;
        $postval['history_title']  = $request->history_title;
        $postval['history_body']  = $request->history_body;

        $update = ModelsStationHistory::find($id)->update($postval);

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

            ModelsStationHistory::find($id)->update($updateVal);
        }

        return $this->sendResponse($update, 'Edit Perawatan Sukses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $title = 'Hapus Riwayat Perawatan';

        $stationAssets = ModelsStationHistory::find($id);
        File::delete($stationAssets->history_tumbnial);
        File::delete($stationAssets->history_imgae);
        $stationAssets->delete();

        return $this->sendResponse($stationAssets, $title . ' sukses');
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}

