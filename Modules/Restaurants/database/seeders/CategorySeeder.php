<?php

namespace Modules\Restaurants\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Restaurants\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Fast Food',
            'Fine Dining',
            'Cafe',
            'Desserts',
            'Healthy',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category],
                ['image' => null] // Defaulting to null, accessor will provide fallback URL
            );
        }
    }
}
