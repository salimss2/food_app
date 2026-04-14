<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAvailability extends Model
{
    protected $table = 'driver_availability';

    protected $fillable = [
        'driver_id',
        'is_online',
        'availability',
        'status',
        'last_updated',
    ];

    public $timestamps = true;
}
