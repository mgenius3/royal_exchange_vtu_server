<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $apiKey;
    protected $apiSecret;
    protected $cloudName;

    /**
     * Initialize Cloudinary credentials from .env.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->apiKey = env('CLOUDINARY_KEY');
        $this->apiSecret = env('CLOUDINARY_SECRET');
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME');

        if (!$this->apiKey || !$this->apiSecret || !$this->cloudName) {
            throw new \Exception('Cloudinary credentials missing in .env');
        }
    }

    /**
     * Upload an image to Cloudinary.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $uploadPreset
     * @return array
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, string $folder, ?string $uploadPreset = 'davyking'): array
    {
        // Verify file
        $filePath = $file->getRealPath();
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception('Image file is not accessible or does not exist.');
        }

        // Prepare request parameters
        $timestamp = time();
        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];
        if ($uploadPreset) {
            $params['upload_preset'] = $uploadPreset;
        }

        // Sort parameters alphabetically
        ksort($params);

        // Build signature string
        $signatureString = '';
        foreach ($params as $key => $value) {
            if ($signatureString !== '') {
                $signatureString .= '&';
            }
            $signatureString .= "$key=$value";
        }
        $signatureString .= $this->apiSecret;
        $signature = sha1($signatureString);

        // Log signature for debugging
        Log::debug('Cloudinary signature', [
            'signature_string' => $signatureString,
            'signature' => $signature,
        ]);

        // Send HTTP POST request
        $response = Http::asMultipart()
            ->attach('file', file_get_contents($filePath), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", array_merge($params, [
                'api_key' => $this->apiKey,
                'signature' => $signature,
            ]));

        if ($response->failed()) {
            throw new \Exception('Cloudinary upload failed: ' . json_encode($response->json()));
        }

        $result = $response->json();
        return [
            'secure_url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    /**
     * Delete an image from Cloudinary by public ID.
     *
     * @param string $publicId
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            $timestamp = time();
            $params = [
                'public_id' => $publicId,
                'timestamp' => $timestamp,
            ];
            ksort($params);
            $signatureString = '';
            foreach ($params as $key => $value) {
                if ($signatureString !== '') {
                    $signatureString .= '&';
                }
                $signatureString .= "$key=$value";
            }
            $signatureString .= $this->apiSecret;
            $signature = sha1($signatureString);

            $response = Http::post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                'public_id' => $publicId,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

            return !$response->failed();
        } catch (\Exception $e) {
            Log::warning('Failed to delete Cloudinary image', ['public_id' => $publicId, 'error' => $e->getMessage()]);
            return false;
        }
    }
}