<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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
        $params = [
            'timestamp' => $timestamp,
            'folder'    => $folder,
        ];

        $params['signature'] = $this->generateSignature($params);
        $params['api_key']   = $this->apiKey;

        $response = Http::asMultipart()
            ->attach('file', $file->get(), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/image/upload", $params);

        if ($response->failed()) {
            \Log::error('Cloudinary upload failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        return $response->json('secure_url');
    }

    public function delete(string $url): bool
    {
        $publicId = $this->getPublicIdFromUrl($url);
        if (!$publicId) return false;

        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'public_id' => $publicId,
        ];

        $params['signature'] = $this->generateSignature($params);
        $params['api_key']   = $this->apiKey;

        $response = Http::asForm()->post("{$this->baseUrl}/image/destroy", $params);

        return $response->successful() && $response->json('result') === 'ok';
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
