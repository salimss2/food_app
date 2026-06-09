<?php

namespace Modules\Restaurants\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MealVariantResource extends JsonResource
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
            'meal_id' => $this->meal_id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
