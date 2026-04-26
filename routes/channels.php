<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('restaurant.{id}', function ($user, $id) {
    return true; // Simple allow for testing, can be scoped later
});

Broadcast::channel('drivers', function ($user) {
    return true; // Simple allow for testing
});
