<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use Blameable;

    protected $primaryKey = 'absen_id';
  
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'absen_time',
        'absen_file',
    ];
    public function user(){
        return $this->hasOne('App\Models\User','id','created_by');
    }

    public function getAbsenTimeAttribute($value)
    {
        return (new Carbon($value))->format('D, Y-M-d H:m');
    }
}
