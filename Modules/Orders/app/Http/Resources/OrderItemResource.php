<?php

namespace Modules\Orders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurants\Http\Resources\MealResource;

class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'meal_id' => $this->meal_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'customizations' => $this->customizations,
            'special_instructions' => $this->special_instructions,
            'meal' => new MealResource($this->whenLoaded('meal')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
