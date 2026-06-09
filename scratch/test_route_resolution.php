<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "--- Testing Route Resolution ---\n";

    $inboxUrl = route('admin.notifications.inbox');
    echo "admin.notifications.inbox -> " . $inboxUrl . "\n";

    $markAllReadUrl = route('admin.notifications.inbox.mark-all-read');
    echo "admin.notifications.inbox.mark-all-read -> " . $markAllReadUrl . "\n";

    $readUrl = route('admin.notifications.inbox.read', ['id' => '123-uuid']);
    echo "admin.notifications.inbox.read -> " . $readUrl . "\n";

    echo "\nRoute Resolution: SUCCESS!\n";

} catch (\Exception $e) {
    echo "Error resolving routes: " . $e->getMessage() . "\n";
}
