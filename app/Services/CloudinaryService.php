<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->cloudName = config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME'));
        $this->apiKey    = config('cloudinary.api_key', env('CLOUDINARY_API_KEY'));
        $this->apiSecret = config('cloudinary.api_secret', env('CLOUDINARY_API_SECRET'));
        $this->baseUrl   = "https://api.cloudinary.com/v1_1/{$this->cloudName}";
    }

    public function credentialsValid(): bool
    {
        return $this->cloudName && $this->apiKey && $this->apiSecret;
    }

    public function upload(UploadedFile $file, string $folder = 'projects'): ?string
    {
        $timestamp = time();
        $fields = [
            'timestamp' => (string) $timestamp,
            'folder'    => $folder,
        ];

        $fields['signature'] = $this->generateSignature($fields);
        $fields['api_key']   = $this->apiKey;

        $curlFile = new \CurlFile($file->getRealPath(), $file->getMimeType(), $file->getClientOriginalName());
        $fields['file'] = $curlFile;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode >= 400) {
            \Log::error('Cloudinary upload failed', [
                'http_code'  => $httpCode,
                'curl_error' => $curlError,
                'response'   => $raw,
            ]);
            return null;
        }

        $data = json_decode($raw, true);
        return $data['secure_url'] ?? null;
    }

    public function delete(string $url): bool
    {
        $publicId = $this->getPublicIdFromUrl($url);
        if (!$publicId) return false;

        $timestamp = time();
        $fields = [
            'timestamp' => (string) $timestamp,
            'public_id' => $publicId,
        ];

        $fields['signature'] = $this->generateSignature($fields);
        $fields['api_key']   = $this->apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/image/destroy");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) return false;

        $data = json_decode($raw, true);
        return ($data['result'] ?? null) === 'ok';
    }

    protected function generateSignature(array $params): string
    {
        ksort($params);
        $signStr = collect($params)
            ->map(fn($v, $k) => "{$k}={$v}")
            ->implode('&');

        return sha1($signStr . $this->apiSecret);
    }

    protected function getPublicIdFromUrl(string $url): ?string
    {
        if (!str_contains($url, 'cloudinary')) return null;

        preg_match('~/image/upload/(?:v\d+/)?(.+?)(?:\.[a-z]+)?$~', $url, $m);
        return $m[1] ?? null;
    }
}
