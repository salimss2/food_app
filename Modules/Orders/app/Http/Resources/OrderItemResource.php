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
            'product_id' => $this->meal_id,
            'name' => $this->name ?? ($this->meal->name ?? 'Unknown'),
            'quantity' => (int) $this->quantity,
            'price' => (float) $this->price,
            'subtotal' => (float) $this->subtotal,
            'type' => $this->type ?? 'regular_meal',
            'combo_meals' => $this->combo_meals,
            'customizations' => $this->customizations,
            'special_instructions' => $this->special_instructions,
            'meal' => $this->whenLoaded('meal', function () {
                return $this->meal ? new MealResource($this->meal) : null;
            }),
            'offer' => $this->whenLoaded('offer', function () {
                return $this->offer ? [
                    'id' => $this->offer->id,
                    'title' => $this->offer->title,
                    'combo_price' => (float) $this->offer->combo_price,
                    'image_url' => $this->offer->image_url,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
