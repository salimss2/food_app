<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistanceSlab extends Model
{
    protected $fillable = [
        'min_distance',
        'max_distance',
        'total_fee',
        'driver_share',
        'platform_share',
    ];
}
