<?php

namespace App\Http\Controllers\Api;

use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class AbsenController extends BaseController
{

    public function index(Request $request)
    {
        $title = 'Data Absensi';
        $subTitle = '';


        $absen = Absen::get();

        foreach ($absen as $key => $value) {
            $value->user;
        }


        return $this->sendResponse($absen, $title . ' data found');
    }

    public function cekAbsen(Request $request)
    {

        $title = 'Cek Absen Hari ini';
        $subTitle = '';
        $status = false;

        $absen = Absen::where(DB::raw('DATE_FORMAT(absen_time, "%Y-%m-%d")'), date('Y-m-d'))
            ->first();

        if (isset($absen->absen_id)) {
            $status = true;
        }
        $response['status'] = $status;

        return $this->sendResponse($response, $title . ' data found');
    }

    public function store(Request $request)
    {
        $title = 'Absensi ';
        $request->validate([
            'user_id' => ['required',],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'absen_file' => ['required', 'mimes:jpg,jpeg,png'],
        ]);

        $postval['user_id']  = $request->user_id;
        $postval['latitude']  = $request->latitude;
        $postval['longitude']  = $request->longitude;
        $postval['absen_time']  = date('Y-m-d H:m:i');

        if ($request->hasFile('absen_file')) {

            //get file extension
            $extension = $request->file('absen_file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = time() . '.' . $extension;

            $request->file('absen_file')->storeAs('public/absen', $filenametostore);

            $postval['absen_file']  = 'storage/absen/' . $filenametostore;
        }

        $insert =  Absen::create($postval);

        return $this->sendResponse($insert, $title . ' Sukses');
    }
}
