<?php

namespace Modules\Restaurants\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            // Absolute URL so Flutter can display directly with Image.network
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'available' => (bool) $this->available,
            'is_available' => (bool) $this->available,
            'restaurant_id' => $this->restaurant_id,
            'meal_category_id' => $this->meal_category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
