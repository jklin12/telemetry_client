<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationAssets extends Model
{ 
    use Blameable;

    protected $table = 'sch_station_assets';
    protected $primaryKey = 'assets_id';

    protected $fillable = [
        'station',
        'asset_name',
        'asset_brand',
        'asset_type',
        'asset_serial_number',
        'asset_spesification',
        'asset_year',
        'asset_tumbnial',
        'asset_imgae',
    ];
    protected $guarded = ['assets_id'];
    public $timestamps = true;

    public function stations(){
        return $this->belongsTo('App\Models\StationModel','station');
    }
   
}
