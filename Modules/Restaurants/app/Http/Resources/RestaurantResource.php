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
            'status' => $this->status,
            'category' => $this->category,
            'account_status' => $this->account_status,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random',
            'meal_categories' => CategoryResource::collection($this->whenLoaded('mealCategories')),
            'meals' => MealResource::collection($this->whenLoaded('meals')),
            'offers' => $this->whenLoaded('offers'), // Can add OfferResource later if needed
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
