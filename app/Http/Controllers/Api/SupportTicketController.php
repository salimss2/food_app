<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Submit a new support ticket / complaint / inquiry
     * POST /api/v1/support/tickets
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:complaint,inquiry',
            'subject' => 'required|string|min:3|max:255',
            'message' => 'required|string|min:5|max:2000',
            'category' => 'nullable|string|max:100',
            'related_id' => 'nullable|string|max:100',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في تجميع البيانات المدخلة.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $ticketCode = SupportTicket::generateTicketCode($request->type);

        $ticket = SupportTicket::create([
            'ticket_code' => $ticketCode,
            'user_id' => $user->id,
            'type' => $request->type,
            'category' => $request->category ?? 'General',
            'related_id' => $request->related_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
            'priority' => $request->priority ?? 'medium',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال تذكرتك بنجاح وسيقوم فريق الدعم الفني بمراجعتها في أقرب وقت.',
            'data' => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'type' => $ticket->type,
                'category' => $ticket->category,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }
}
