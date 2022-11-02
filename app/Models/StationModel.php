<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationModel extends Model
{
    use HasFactory;

    protected $table = 'sch_data_station';
    protected $primaryKey = 'station_id';
    public $incrementing = false;

    protected $fillable = [
        'station_id',
        'station_name',
        'station_lat',
        'station_long',
        'station_river',
        'station_equipment',
        'station_prod_year',
        'station_instalaton_date',
        'station_authority',
        'station_guardsman',
        'station_reg_number',
    ];

    public $timestamps = false;
}
