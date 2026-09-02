<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'support_tickets';

    protected $fillable = [
        'ticket_code',
        'user_id',
        'type',
        'category',
        'related_id',
        'subject',
        'message',
        'status',
        'priority',
        'admin_id',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Customer / User who submitted the ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Admin user who responded to the ticket
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Auto-generate human-friendly unique ticket code
     */
    public static function generateTicketCode(string $type = 'inquiry'): string
    {
        $prefix = ($type === 'complaint') ? 'CP-' : 'INQ-';
        $randomNum = rand(1000, 9999);
        $code = $prefix . $randomNum;

        while (static::where('ticket_code', $code)->exists()) {
            $randomNum = rand(1000, 9999);
            $code = $prefix . $randomNum;
        }

        return $code;
    }
}
