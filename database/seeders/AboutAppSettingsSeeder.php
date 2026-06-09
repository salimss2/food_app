<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AboutAppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'about_app_description'],
            ['value' => 'تطبيق FastGrab هو وجهتك الأولى لطلب الطعام أونلاين. نحن نهدف إلى توفير تجربة طلب سلسة وسريعة تربطك بأفضل المطاعم المحلية في مدينتك.']
        );

        Setting::updateOrCreate(
            ['key' => 'app_version'],
            ['value' => '1.0.0']
        );

        Setting::updateOrCreate(
            ['key' => 'about_app_features'],
            ['value' => json_encode(["توصيل سريع وموثوق", "تنوع كبير في المطاعم", "طرق دفع آمنة ومتعددة"])]
        );
    }
}
