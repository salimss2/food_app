<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Delivery\Database\Factories\DeliveryTaskFactory;

class DeliveryTask extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DeliveryTaskFactory
    // {
    //     // return DeliveryTaskFactory::new();
    // }
}
