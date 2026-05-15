<?php

namespace Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Support\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'type' => ['required', 'string', 'in:complaint,inquiry'],
            'subject' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
        ]);

        // 2. Save the record
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(), // Automatically attach authenticated user ID
            'type' => $request->type,
            'subject' => $request->subject,
            'details' => $request->details,
            'message' => $request->details,
            'status' => 'pending',
        ]);

        // 3. Return professional JSON response
        return response()->json([
            'status' => true,
            'message' => 'تم إرسال تذكرتك بنجاح! فريق الدعم سيتواصل معك قريباً. 🎉',
            'data' => $ticket
        ], 201);
    }
}
