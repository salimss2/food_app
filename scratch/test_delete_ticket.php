<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SupportTicket;
use App\Models\User;
use App\Http\Controllers\Admin\SupportTicketManagementController;

$user = User::first();
auth()->setUser($user);

// Create temporary ticket for deletion
$ticket = SupportTicket::create([
    'ticket_code' => SupportTicket::generateTicketCode('complaint'),
    'user_id' => $user->id,
    'type' => 'complaint',
    'subject' => 'تذكرة مؤقتة للاختبار الحذف',
    'message' => 'سيتم حذف هذه التذكرة لاختبار Soft Delete',
    'status' => 'pending',
]);

echo "Created Ticket ID: {$ticket->id}\n";

$controller = new SupportTicketManagementController();
$response = $controller->destroy($ticket->id);
$responseData = json_decode($response->getContent(), true);

echo "Delete API Response:\n";
print_r($responseData);

// Check if soft deleted
$exists = SupportTicket::where('id', $ticket->id)->exists();
$trashed = SupportTicket::withTrashed()->where('id', $ticket->id)->exists();

echo "Exists in normal query: " . ($exists ? 'YES' : 'NO') . "\n";
echo "Exists in trashed query: " . ($trashed ? 'YES' : 'NO') . "\n";

if (!$exists && $trashed) {
    echo "SUCCESS: Soft Delete verified successfully!\n";
} else {
    echo "FAILURE: Soft Delete failed.\n";
}
