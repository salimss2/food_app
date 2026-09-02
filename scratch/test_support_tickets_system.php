<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SupportTicketManagementController;

echo "--- Testing Support & Tickets Management System ---\n";

$user = User::first() ?? User::factory()->create();
$adminUser = $user;

// 1. Create a Complaint Ticket
$complaintCode = SupportTicket::generateTicketCode('complaint');
$complaintTicket = SupportTicket::create([
    'ticket_code' => $complaintCode,
    'user_id' => $user->id,
    'type' => 'complaint',
    'category' => 'Order',
    'related_id' => '#ORD-9901',
    'subject' => 'تأخير في تسليم الطلب وبلاغ السائق',
    'message' => 'لقد تأخر الطلب أكثر من ساعة والوجبة وصلت باردة، أطالب بالتعويض.',
    'status' => 'pending',
    'priority' => 'high',
]);

echo "Created Complaint Ticket: Code={$complaintTicket->ticket_code}, ID={$complaintTicket->id}\n";

// 2. Create an Inquiry Ticket
$inquiryCode = SupportTicket::generateTicketCode('inquiry');
$inquiryTicket = SupportTicket::create([
    'ticket_code' => $inquiryCode,
    'user_id' => $user->id,
    'type' => 'inquiry',
    'category' => 'Payment',
    'related_id' => null,
    'subject' => 'استفسار عن طرق الدفع بالبطاقة',
    'message' => 'هل تدعم المنصة بطاقات الماستركارد حالياً؟',
    'status' => 'pending',
    'priority' => 'medium',
]);

echo "Created Inquiry Ticket: Code={$inquiryTicket->ticket_code}, ID={$inquiryTicket->id}\n";

// 3. Test Admin Stats & Listing Controller
$controller = new SupportTicketManagementController();
$req = new Request(['type' => 'all', 'status' => 'all']);

$response = $controller->getStatsAndTickets($req);
$responseData = json_decode($response->getContent(), true);

echo "KPI Stats Output:\n";
print_r($responseData['kpi']);

// 4. Test Admin Response Submission
$respondReq = new Request([
    'status' => 'resolved',
    'admin_response' => 'تم التعويض وإضافة رصيد مجاني لحسابك، نعتذر عن التأخير.',
]);

// Authenticate mock admin
auth()->setUser($adminUser);
$respondResp = $controller->respond($respondReq, $complaintTicket->id);
$respondData = json_decode($respondResp->getContent(), true);

echo "Response Submission Result:\n";
print_r($respondData);

// 5. Verify KPI recalculation post-response
$newResponse = $controller->getStatsAndTickets(new Request());
$newResponseData = json_decode($newResponse->getContent(), true);

echo "Updated KPI Stats Output:\n";
print_r($newResponseData['kpi']);

// 6. Verify Database Notification record created
$notification = \Illuminate\Support\Facades\DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($notification) {
    echo "Created Notification Record in DB:\n";
    echo "ID: {$notification->id}\n";
    echo "Type: {$notification->type}\n";
    echo "Data: {$notification->data}\n";
} else {
    echo "⚠️ No notification record found in database!\n";
}

echo "--- Verification Completed Successfully ---\n";
