<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getPrivacyPolicy()
    {
        // البحث عن سياسة الخصوصية في قاعدة البيانات
        $policy = Setting::where('key', 'privacy_policy')->first();

        // إذا وجدها، يرسلها. وإذا لم يجدها، يرسل نصاً افتراضياً لكي لا يتعطل التطبيق
        if ($policy) {
            $content = $policy->value;
        } else {
            $content = "هذا نص تجريبي لسياسة الخصوصية قادم من لارافل. يمكنك تعديل هذا النص لاحقاً من لوحة تحكم الإدارة الخاص بك.";
        }

        return response()->json([
            'status' => true,
            'content' => $content
        ], 200);
    }

    public function getAboutAppData()
    {
        $description = Setting::where('key', 'about_app_description')->first();
        $version = Setting::where('key', 'app_version')->first();
        $features = Setting::where('key', 'about_app_features')->first();

        return response()->json([
            'status' => true,
            'data' => [
                'description' => $description ? $description->value : 'تطبيق FastGrab لطلب الطعام.',
                'version' => $version ? $version->value : '1.0.0',
                'features' => $features ? json_decode($features->value) : []
            ]
        ], 200);
    }
}