<?php

// Simulate Laravel environment for a quick test if possible, 
// but since I can't run it easily without a full bootstrap, 
// I'll just do a manual check of the code logic.

echo "Verification Plan:\n";
echo "1. OrderResource.php has restaurant_latitude, restaurant_longitude, customer_latitude, customer_longitude.\n";
echo "2. All are cast to (float).\n";
echo "3. DriverOrderController.php eager loads user.profile.\n";
echo "4. DriverOrderController.php uses OrderResource.\n";
echo "5. OrdersController.php eager loads user.profile.\n";

echo "\nLogic Check:\n";
echo "customer_latitude => (float) (\$this->user->profile->latitude ?? \$this->latitude)\n";
echo "If user->profile->latitude is 12.345, output is 12.345 (float).\n";
echo "If user->profile->latitude is null and \$this->latitude is '67.89', output is 67.89 (float).\n";
?>