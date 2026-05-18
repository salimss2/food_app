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
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value ? (float) $this->discount_value : null,
            'discount_start' => $this->discount_start ? $this->discount_start->toISOString() : null,
            'discount_end' => $this->discount_end ? $this->discount_end->toISOString() : null,
            'price_after_discount' => (float) $this->price_after_discount,
            // Absolute URL so Flutter can display directly with Image.network
            'image_url' => $this->image ? \Illuminate\Support\Facades\Storage::url($this->image) : null,
            'image_full_url' => $this->image_full_url,
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
