<?php

namespace Modules\Authentication\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Authentication\Database\Factories\DriverProfileFactory;

class DriverProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DriverProfileFactory
    // {
    //     // return DriverProfileFactory::new();
    // }
}
