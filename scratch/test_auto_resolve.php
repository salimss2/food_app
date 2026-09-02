<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SupportTicketManagementController;

$user = User::first();

// Create pending complaint ticket
$ticket = SupportTicket::create([
    'ticket_code' => SupportTicket::generateTicketCode('complaint'),
    'user_id' => $user->id,
    'type' => 'complaint',
    'subject' => 'شكوى معلقة للاختبار',
    'message' => 'اختبار التغيير التلقائي للحالة إلى تم الحل',
    'status' => 'pending',
]);

echo "Created Ticket ID {$ticket->id} with status: {$ticket->status}\n";

// Respond with status set to 'pending' explicitly
$controller = new SupportTicketManagementController();
auth()->setUser($user);

$req = new Request();
$req->replace([
    'status' => 'pending',
    'admin_response' => 'تم استلام الشكوى وإغلاقها بتأكيد الرد.',
]);

$response = $controller->respond($req, $ticket->id);
$responseData = json_decode($response->getContent(), true);

$ticket->refresh();
echo "Post-Response Ticket Status: {$ticket->status}\n";

if ($ticket->status === 'resolved') {
    echo "SUCCESS: Status automatically resolved on response!\n";
} else {
    echo "FAILURE: Status stayed as {$ticket->status}\n";
}
