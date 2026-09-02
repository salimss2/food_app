<?php

namespace Modules\Orders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurants\Http\Resources\RestaurantResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'group_id' => $this->group_id,
            'is_multi_vendor' => $this->isMultiVendorOrder(),
            'group_grand_total' => (float) $this->group_grand_total,
            'sibling_sub_orders_count' => !empty($this->group_id) ? $this->groupSiblingOrders()->count() : 0,
            'user_id' => $this->user_id,
            'restaurant_id' => $this->restaurant_id,
            'driver_id' => $this->driver_id,
            'payment_method' => $this->payment_method,
            'total' => (float) $this->total,
            'total_price' => $this->total_price,
            'driver_earning' => (float) $this->driver_earning,
            'driver_commission' => (float) $this->driver_commission,
            'platform_commission' => (float) $this->platform_commission,
            'delivery_fee' => (float) $this->delivery_fee,
            'delivery_distance' => (float) $this->delivery_distance,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'customer_notes' => $this->customer_notes,
            'restaurant_latitude' => (float) ($this->restaurant->latitude ?? 0),
            'restaurant_longitude' => (float) ($this->restaurant->longitude ?? 0),
            'customer_latitude' => (float) ($this->user->profile->latitude ?? $this->latitude),
            'customer_longitude' => (float) ($this->user->profile->longitude ?? $this->longitude),
            'restaurant_lat' => (float) ($this->restaurant->latitude ?? 0),
            'restaurant_lng' => (float) ($this->restaurant->longitude ?? 0),
            'customer_lat' => (float) ($this->latitude ?? ($this->user->profile->latitude ?? 0)),
            'customer_lng' => (float) ($this->longitude ?? ($this->user->profile->longitude ?? 0)),
            'driver_lat' => $this->driver_id && $this->driver && $this->driver->driverProfile ? (float) $this->driver->driverProfile->latitude : null,
            'driver_lng' => $this->driver_id && $this->driver && $this->driver->driverProfile ? (float) $this->driver->driverProfile->longitude : null,
            'customer_location' => [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ],
            'restaurant_location' => [
                'latitude' => (float) ($this->restaurant->latitude ?? null),
                'longitude' => (float) ($this->restaurant->longitude ?? null),
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'user' => $this->whenLoaded('user'), // User model doesn't have a resource yet, returning raw or we can create one
            'driver' => $this->whenLoaded('driver'),
            'payment' => $this->whenLoaded('payment'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
