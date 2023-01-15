<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterLevel60Model extends Model
{
    use HasFactory;

    protected $table = 'sch_data_waterlevel_60';
    protected $primaryKey = 'water_level_id';
    public $incrementing = false;

    protected $fillable = [
        'water_level_id',
        'station',
        'water_level_date',
        'water_level_time',
        'water_level_hight',
    ];

    public $timestamps = false;
}
