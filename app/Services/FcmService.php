<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $projectId;
    protected $credentials = null;

    public function __construct()
    {
        $this->loadCredentials();
        $this->projectId = $this->credentials['project_id'] ?? config('services.fcm.project_id');
    }

    /**
     * Load Firebase credentials from Cloudflare R2 / S3 storage (firebase_s3) or local fallback.
     */
    protected function loadCredentials()
    {
        // 1. Try to fetch from 'firebase_s3' Cloudflare R2 / S3 disk
        try {
            if (\Illuminate\Support\Facades\Storage::disk('firebase_s3')->exists('firebase-credentials.json')) {
                $content = \Illuminate\Support\Facades\Storage::disk('firebase_s3')->get('firebase-credentials.json');
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $this->credentials = $decoded;
                    return;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to load Firebase credentials from cloud disk: ' . $e->getMessage());
        }

        // 2. Fallback to local storage path (e.g. for local development)
        $localPath = storage_path('app/firebase-credentials.json');
        if (file_exists($localPath)) {
            $content = file_get_contents($localPath);
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $this->credentials = $decoded;
            }
        }
    }

    /**
     * Get OAuth 2.0 Access Token natively using JWT.
     */
    protected function getAccessToken()
    {
        return Cache::remember('fcm_access_token', 3500, function () {
            if (!$this->credentials) {
                Log::error('FCM credentials not loaded from cloud or local storage.');
                return null;
            }

            $credentials = $this->credentials;
            $now = time();

            $header = base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64UrlEncode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signature = '';
            openssl_sign("$header.$payload", $signature, $credentials['private_key'], 'SHA256');
            $signature = base64UrlEncode($signature);

            $jwt = "$header.$payload.$signature";

            $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('FCM OAuth token exchange failed: ' . $response->body());
                return null;
            }

            return $response->json('access_token');
        });
    }

    /**
     * Send notification via FCM HTTP v1.
     */
    public function sendNotification($deviceToken, $title, $body, $dataPayload = [])
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        // FCM v1 requires all data values to be strings
        $formattedData = array_map(fn($val) => (string) $val, $dataPayload);

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $formattedData,
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withoutVerifying()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('FCM Notification failed: ' . $response->body());
            return false;
        }

        return true;
    }

    /**
     * Send notifications to multiple devices.
     */
    public function sendToMultipleDevices(array $deviceTokens, $title, $body, $dataPayload = [])
    {
        $successCount = 0;
        foreach ($deviceTokens as $token) {
            if ($this->sendNotification($token, $title, $body, $dataPayload)) {
                $successCount++;
            }
        }
        return $successCount;
    }
}

/**
 * Helper function for Base64Url encoding.
 */
if (!function_exists('base64UrlEncode')) {
    function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
