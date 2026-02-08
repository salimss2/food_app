<?php

// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    Modules\Authentication\app\Providers\AuthenticationServiceProvider::class, // تأكد من وجود هذا السطر
];
