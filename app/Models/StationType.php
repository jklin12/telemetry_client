<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationType extends Model
{
    use HasFactory;
    protected $table = 'sch_station_types';
    
    protected $fillable = [
        'station_id',
        'station_type',
        'aler_column'];
}
