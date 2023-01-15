<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WireVibration30Model extends Model
{
    use HasFactory;
    protected $table = 'sch_data_wirevibration_30';
    protected $primaryKey = 'wire_vibration_id';
    public $incrementing = false;

    protected $fillable = [
        'wire_vibration_id',
        'station',
        'wire_vibration_date',
        'wire_vibration_time',
        'wire',
        'vibration',
    ];

    public $timestamps = false;
}
