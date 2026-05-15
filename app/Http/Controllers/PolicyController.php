<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Get the privacy policy content.
     */
    public function getPrivacyPolicy()
    {
        $setting = Setting::where('key', 'privacy_policy')->first();

        $content = $setting ? $setting->value : "سياسة الخصوصية\n\nنحن نهتم بخصوصيتك. هذه سياسة الخصوصية الافتراضية.";

        return response()->json([
            'status' => true,
            'content' => $content,
        ]);
    }
}
