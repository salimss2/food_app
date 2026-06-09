<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// ── Language Switcher ──────────────────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'])) {
        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/debug-test', function () {
    throw new Exception("Laravel is working!");
});

Route::get('/test-payment', function () {
    \Modules\Orders\Models\Order::create([
        'user_id' => 1,
        'restaurant_id' => 8,
        'total' => 150.00, // تم تعديل الاسم هنا ليطابق قاعدة بياناتك
        'payment_method' => 'bank_transfer',
        'payment_status' => 'pending_verification',
        'status' => 'pending',
        'receipt_image' => 'https://via.placeholder.com/600x400.png?text=Fake+Bank+Receipt'
    ]);
    return 'تم إنشاء الطلب الوهمي بنجاح! اذهب إلى لوحة التحكم الآن.';
});

use App\Events\TestRealtimeEvent;

Route::get('/test-reverb', function () {
    event(new TestRealtimeEvent("🔥 Hello from Reverb"));
    return "Event Sent!";
});

// ── Cloudflare R2 / S3 Storage Redirect for Serverless/Cloud Hosting ────────
Route::get('storage/{path}', function ($path) {
    // 1. Clean path and strip any leading 'public/' if present
    $cleanPath = ltrim($path, '/');
    $noPublicPath = preg_replace('/^public\//', '', $cleanPath);

    // 2. Determine correct target path based on R2 existence checks
    $targetPath = $cleanPath;
    try {
        if (\Illuminate\Support\Facades\Storage::disk('s3')->exists($cleanPath)) {
            $targetPath = $cleanPath;
        } elseif (\Illuminate\Support\Facades\Storage::disk('s3')->exists($noPublicPath)) {
            $targetPath = $noPublicPath;
        } elseif (\Illuminate\Support\Facades\Storage::disk('s3')->exists('public/' . $cleanPath)) {
            $targetPath = 'public/' . $cleanPath;
        }
    } catch (\Exception $e) {
        // Fallback to noPublicPath if S3 connection is offline or has issues
        $targetPath = $noPublicPath;
    }

    // 3. Redirect to the public Cloudflare R2 URL
    return redirect(\Illuminate\Support\Facades\Storage::disk('s3')->url($targetPath));
})->where('path', '.*');