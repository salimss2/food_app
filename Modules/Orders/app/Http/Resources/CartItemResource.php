<?php

namespace Modules\Orders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurants\Http\Resources\MealResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $quantity = (int) $this->quantity;
        $subtotal = (float) $this->subtotal;
        $unitPrice = $quantity > 0 ? round($subtotal / $quantity, 2) : 0.0;

        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'meal_id' => $this->meal_id,
            'offer_id' => $this->offer_id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'customizations' => $this->customizations ?? [],
            'options' => $this->customizations ?? [],
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
