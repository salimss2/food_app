<?php

namespace Modules\Restaurants\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MealOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $optionName = $this->name ?? $this->option_name ?? '';
        $price = (float) ($this->price ?? $this->additional_price ?? 0);

        return [
            'id' => $this->id,
            'meal_id' => $this->meal_id,
            'name' => $optionName,
            'option_name' => $optionName,
            'price' => $price,
            'additional_price' => $price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
