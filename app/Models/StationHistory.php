<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationHistory extends Model
{
    use Blameable;

    protected $table = 'sch_station_history';
    protected $primaryKey = 'history_id';

    protected $fillable = [
        'station',
        'assets',
        'history_title',
        'history_body',
        'history_tumbnial',
        'history_imgae',

    ];
    protected $guarded = ['history_id'];
    public $timestamps = true;

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->format('D, Y-M-d H:m');
    }

    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->format('D, Y-M-d H:m');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User','created_by');
    }

    public function editor(){
        return $this->belongsTo('App\Models\User','updated_by');
    }

    public function stations(){
        return $this->belongsTo('App\Models\StationModel','station');
    }
    
    public function asset(){
        return $this->belongsTo('App\Models\StationAssets','assets');
    }
    
}
