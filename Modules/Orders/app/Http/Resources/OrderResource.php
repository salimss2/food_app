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
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'restaurant_id' => $this->restaurant_id,
            'driver_id' => $this->driver_id,
            'payment_method' => $this->payment_method,
            'total' => $this->total,
            'total_price' => $this->total_price,
            'driver_commission' => (float) $this->driver_commission,
            'platform_commission' => (float) $this->platform_commission,
            'delivery_fee' => (float) $this->delivery_fee,
            'delivery_distance' => (float) $this->delivery_distance,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'customer_notes' => $this->customer_notes,
            'customer_location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'restaurant_location' => [
                'latitude' => $this->restaurant->latitude ?? null,
                'longitude' => $this->restaurant->longitude ?? null,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'user' => $this->whenLoaded('user'), // User model doesn't have a resource yet, returning raw or we can create one
            'driver' => $this->whenLoaded('driver'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
