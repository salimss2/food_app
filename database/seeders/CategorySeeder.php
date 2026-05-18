<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // نرسل الاسم فقط لأن هذا ما يدعمه الجدول حالياً
        $categories = [
            ['name' => 'Fast Food'],
            ['name' => 'Cafe'],
            ['name' => 'Desserts'],
            ['name' => 'Healthy'],
            ['name' => 'Fine Dining'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                $category
            );
        }
    }
}