<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurentRainfallModel extends Model
{
    use HasFactory;

    protected $table = 'sch_curent_rainfall';
    protected $primaryKey = 'rain_fall_id';
    public $incrementing = false;

    protected $fillable = [
        'rain_fall_id',
        'station',
        'rain_fall_date', 
        'rain_fall_10_minut',
        'rain_fall_30_minute',
        'rain_fall_1_hour',
        'rain_fall_3_hour',
        'rain_fall_6_hour',
        'rain_fall_12_hour',
        'rain_fall_24_hour',
        'rain_fall_continuous',
        'rain_fall_effective',
        'rain_fall_effective_intensity',
        'rain_fall_prev_working',
        'rain_fall_working',
        'rain_fall_working_24',
        'rain_fall_remarks'
    ];

    public $timestamps = false;
}
