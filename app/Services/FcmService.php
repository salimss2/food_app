<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = storage_path('app/firebase-credentials.json');
        
        // Try to get project_id from JSON first, fallback to config
        if (file_exists($this->credentialsPath)) {
            $creds = json_decode(file_get_contents($this->credentialsPath), true);
            $this->projectId = $creds['project_id'] ?? config('services.fcm.project_id');
        } else {
            $this->projectId = config('services.fcm.project_id');
        }
    }

    /**
     * Get OAuth 2.0 Access Token natively using JWT.
     */
    protected function getAccessToken()
    {
        return Cache::remember('fcm_access_token', 3500, function () {
            if (!file_exists($this->credentialsPath)) {
                Log::error('FCM credentials file not found at: ' . $this->credentialsPath);
                return null;
            }

            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            $now = time();

            $header = base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64UrlEncode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ]));

            $signature = '';
            openssl_sign("$header.$payload", $signature, $credentials['private_key'], 'SHA256');
            $signature = base64UrlEncode($signature);

            $jwt = "$header.$payload.$signature";

            $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
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
        if (!$accessToken) return false;

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        // FCM v1 requires all data values to be strings
        $formattedData = array_map(fn($val) => (string)$val, $dataPayload);

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
