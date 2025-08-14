<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AuditLog;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log as FacadesLog;

class AdService
{
    /**
     * Create a new ad with the provided data.
     *
     * @param array $data
     * @param int $userId
     * @return Ad
     * @throws \Exception
     */
    public function createAd($data, $userId)
    {
        // Validate that image is provided and is a valid file
        if (!isset($data['image'])) {
            throw new \Exception('Image field is required.');
        }

        if (!$data['image'] instanceof UploadedFile) {
            throw new \Exception('Invalid image file provided.');
        }

        if (!$data['image']->isValid()) {
            throw new \Exception('Uploaded image is not valid: ' . $data['image']->getErrorMessage());
        }

        // Upload image to Cloudinary
        $cloudinaryService = new CloudinaryService();
        $uploadResult = $cloudinaryService->uploadImage(
            $data['image'],
            'ads',
            env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
        );
        $data['image_url'] = $uploadResult['secure_url'];
        $data['cloudinary_public_id'] = $uploadResult['public_id'];

        // Create the ad
        $ad = Ad::create($data);

        // Log the action
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ad_created',
            'details' => json_encode($data),
            'created_at' => now(),
        ]);

        return $ad;
    }

    /**
     * Update an existing ad with the provided data.
     *
     * @param int $adId
     * @param array $data
     * @param int $userId
     * @return Ad
     * @throws \Exception
     */
    public function updateAd($adId, $data, $userId)
    {
        $ad = Ad::findOrFail($adId);
        $oldData = $ad->toArray();

        // Handle image update if provided
        if (isset($data['image'])) {
            if (!$data['image'] instanceof UploadedFile) {
                throw new \Exception('Invalid image file provided.');
            }

            if (!$data['image']->isValid()) {
                throw new \Exception('Uploaded image is not valid: ' . $data['image']->getErrorMessage());
            }

            // Delete previous image from Cloudinary
            if ($ad->cloudinary_public_id) {
                $cloudinaryService = new CloudinaryService();
                $cloudinaryService->deleteImage($ad->cloudinary_public_id);
            }

            // Upload new image
            $cloudinaryService = new CloudinaryService();
            $uploadResult = $cloudinaryService->uploadImage(
                $data['image'],
                'ads',
                env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
            );
            $data['image_url'] = $uploadResult['secure_url'];
            $data['cloudinary_public_id'] = $uploadResult['public_id'];
        }

        // Update ad
        $ad->update($data);

        // Record audit log
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ad_updated',
            'details' => json_encode(['old' => $oldData, 'new' => $data]),
            'created_at' => now(),
        ]);

        return $ad;
    }

    /**
     * Delete an ad by ID.
     *
     * @param int $adId
     * @param int $userId
     * @return void
     * @throws \Exception
     */
    public function deleteAd($adId, $userId)
    {
        $ad = Ad::findOrFail($adId);

        // Delete image from Cloudinary
        if ($ad->cloudinary_public_id) {
            $cloudinaryService = new CloudinaryService();
            $cloudinaryService->deleteImage($ad->cloudinary_public_id);
        }

        // Delete ad
        $ad->delete();

        // Record audit log
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ad_deleted',
            'details' => json_encode(['ad_id' => $adId]),
            'created_at' => now(),
        ]);
    }

    public function getActiveAds()
    {

    
        return Ad::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->get();
    }
}
