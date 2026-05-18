<?php

namespace Modules\Restaurants\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurants\Http\Resources\MealResource;
use Modules\Restaurants\Http\Resources\CategoryResource;

class RestaurantResource extends JsonResource
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
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'is_open' => $this->status === 'open',
            'category' => $this->category,
            'logo' => $this->logo
                ? \Illuminate\Support\Facades\Storage::url(str_contains($this->logo, '/') ? $this->logo : 'restaurants/logos/' . $this->logo)
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random',
            'logo_full_url' => $this->logo_full_url,
            'description' => $this->description,
            'phone' => $this->phone,
            'meal_categories' => CategoryResource::collection($this->whenLoaded('mealCategories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
