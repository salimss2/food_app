<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Payments\Database\Factories\PaymentProofFactory;


class PaymentProof extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    public function payment()
{
    return $this->belongsTo(Payment::class);
}
}
