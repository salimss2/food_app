<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SupportTicketManagementController extends Controller
{
    /**
     * Render the Admin Tickets Web Blade View
     * GET /admin/tickets
     */
    public function indexView()
    {
        return view('admin::tickets');
    }

    /**
     * Get KPI Statistics & Filtered Paginated Tickets List
     * GET /admin/api/support/tickets
     */
    public function getStatsAndTickets(Request $request)
    {
        $allTickets = SupportTicket::all();

        $totalTickets = $allTickets->count();
        $pendingComplaints = $allTickets->where('type', 'complaint')->whereIn('status', ['pending', 'in_progress'])->count();
        $openInquiries = $allTickets->where('type', 'inquiry')->whereIn('status', ['pending', 'in_progress'])->count();

        // Calculate Average Resolution Time
        $resolvedTickets = $allTickets->whereNotNull('responded_at');
        if ($resolvedTickets->count() > 0) {
            $totalHours = 0;
            foreach ($resolvedTickets as $t) {
                $totalHours += $t->created_at->diffInMinutes($t->responded_at) / 60.0;
            }
            $avgHours = round($totalHours / $resolvedTickets->count(), 1);
            $avgResolutionTime = ($avgHours < 1 ? round($avgHours * 60) . ' دقيقة' : $avgHours . ' ساعة');
        } else {
            $avgResolutionTime = 'لا يوجد بعد';
        }

        $typeFilter = strtolower($request->get('type', 'all'));
        $statusFilter = strtolower($request->get('status', 'all'));
        $search = strtolower(trim($request->get('search', '')));
        $perPage = (int) $request->get('per_page', 15);

        $query = SupportTicket::with(['user', 'admin']);

        if ($typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $transformedItems = collect($tickets->items())->map(function ($t) {
            $user = $t->user;
            $userName = $user ? $user->name : 'عميل غير معروف';
            $userPhone = $user ? ($user->phone ?? 'غير محدد') : 'غير محدد';

            $nameParts = explode(' ', trim($userName));
            $initials = count($nameParts) >= 2
                ? mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1)
                : mb_substr($userName, 0, 2);
            $initials = mb_strtoupper($initials);

            $avatar = $user && $user->profile_picture_full_url
                ? $user->profile_picture_full_url
                : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&color=fff&background=4f46e5";

            $createdAt = $t->created_at;
            if ($createdAt->isToday()) {
                $dateStr = 'اليوم، ' . $createdAt->format('h:i A');
            } elseif ($createdAt->isYesterday()) {
                $dateStr = 'أمس، ' . $createdAt->format('h:i A');
            } else {
                $dateStr = $createdAt->format('Y-m-d h:i A');
            }

            return [
                'id' => $t->id,
                'ticket_code' => $t->ticket_code ?? ('TK-' . $t->id),
                'type' => $t->type,
                'category' => $t->category ?? 'عام',
                'related_id' => $t->related_id ?? 'لا يوجد',
                'subject' => $t->subject,
                'message' => $t->message,
                'status' => $t->status,
                'priority' => $t->priority,
                'admin_response' => $t->admin_response,
                'admin_name' => $t->admin ? $t->admin->name : null,
                'responded_at' => $t->responded_at ? $t->responded_at->format('Y-m-d h:i A') : null,
                'created_at' => $dateStr,
                'customer_user' => [
                    'id' => $user ? $user->id : null,
                    'name' => $userName,
                    'phone' => $userPhone,
                    'initials' => $initials,
                    'avatar' => $avatar,
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'kpi' => [
                'total_tickets' => $totalTickets,
                'pending_complaints' => $pendingComplaints,
                'open_inquiries' => $openInquiries,
                'avg_resolution_time' => $avgResolutionTime,
            ],
            'data' => $transformedItems,
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
            ]
        ]);
    }

    /**
     * Respond to & Update Support Ticket Status
     * POST /admin/api/support/tickets/{id}/respond
     */
    public function respond(Request $request, $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على التذكرة المطلوبة.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,resolved,rejected,closed',
            'admin_response' => 'required|string|min:3|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى تقديم بيانات استجابة صالحة.',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = auth()->user();

        $targetStatus = $request->status ?? 'resolved';
        if ($targetStatus === 'pending' && !empty($request->admin_response)) {
            $targetStatus = 'resolved';
        }

        $ticket->update([
            'status' => $targetStatus,
            'admin_response' => $request->admin_response,
            'admin_id' => $admin ? $admin->id : null,
            'responded_at' => now(),
        ]);

        // Dispatch Customer Notification (Database & Push)
        $ticketCode = $ticket->ticket_code ?? ('TK-' . $ticket->id);
        $notifTitle = "رد جديد على تذكرتك #{$ticketCode}";
        $responsePreview = 'قامت الإدارة بالرد على تذكرتك: "' . Str::limit($request->admin_response, 100) . '"';

        $dataPayload = [
            'title'          => $notifTitle,
            'message'        => $responsePreview,
            'body'           => $responsePreview,
            'description'    => $responsePreview,
            'subtitle'       => $responsePreview,
            'content'        => $responsePreview,
            'type'           => 'ticket_response',
            'ticket_id'      => $ticket->id,
            'ticket_code'    => $ticketCode,
            'admin_response' => $ticket->admin_response,
            'status'         => $ticket->status,
        ];

        // 1. Create Database Notification Record
        try {
            $customer = $ticket->user;
            if ($customer) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\\Notifications\\TicketRespondedNotification',
                    'notifiable_type' => get_class($customer),
                    'notifiable_id' => $customer->id,
                    'data' => json_encode($dataPayload, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Database notification insertion failed for ticket #' . $ticket->id . ': ' . $e->getMessage());
        }

        // 2. Dispatch Push Notification via FCM
        try {
            $customer = $ticket->user;
            if ($customer && !empty($customer->fcm_token) && class_exists(FcmService::class)) {
                $fcmService = app(FcmService::class);
                $fcmService->sendNotification(
                    $customer->fcm_token,
                    $notifTitle,
                    $responsePreview,
                    [
                        'message'        => (string) $responsePreview,
                        'body'           => (string) $responsePreview,
                        'description'    => (string) $responsePreview,
                        'subtitle'       => (string) $responsePreview,
                        'content'        => (string) $responsePreview,
                        'type'           => 'ticket_response',
                        'ticket_id'      => (string) $ticket->id,
                        'ticket_code'    => (string) $ticketCode,
                        'status'         => (string) $ticket->status,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::warning('FCM push notification failed for ticket #' . $ticket->id . ': ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث التذكرة وإرسال الرد بنجاح!',
            'data' => [
                'id' => $ticket->id,
                'status' => $ticket->status,
                'admin_response' => $ticket->admin_response,
                'responded_at' => $ticket->responded_at->format('Y-m-d h:i A'),
            ]
        ]);
    }

    /**
     * Soft Delete a support ticket
     */
    public function destroy($id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'تذكرة الدعم غير موجودة.',
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف تذكرة الدعم بنجاح.',
        ]);
    }
}
