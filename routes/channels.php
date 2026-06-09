<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('restaurant.{id}', function ($user, $id) {
    $restaurant = \Modules\Restaurants\Models\Restaurant::find($id);
    return $restaurant && ((int) $user->id === (int) $restaurant->owner_id || (int) $user->id === (int) $restaurant->user_id);
});

Broadcast::channel('drivers.available', function ($user) {
    return $user->hasRole('driver');
});

Broadcast::channel('admin.orders', function ($user) {
    return $user->hasRole('System Admin');
});
