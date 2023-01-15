<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow30Model extends Model
{
    use HasFactory;

    protected $table = 'sch_data_flow_30';
    protected $primaryKey = 'flow_id';
    public $incrementing = false;

    protected $fillable = [
        'flow_id',
        'station',
        'flow_date',
        'flow_time',
        'flow',
    ];

    public $timestamps = false;
}
