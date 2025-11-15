<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Exception;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        //$this->imageManager = ImageManager::withDriver('gd');
        $this->imageManager = new ImageManager(new GdDriver());
    }

    public function uploadImage(UploadedFile $file, string $folder = 'images', $width = 300, $height = 150)
    {
        try {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = "$folder/$filename";


            $image = $this->imageManager->read($file->getPathname())->cover($width, $height);

            $encoded = (string) $image->encode();

            //Storage::disk('central_public')->makeDirectory($folder);

            $result = Storage::disk('central_public')->put($path, $encoded);


            if (!$result) {
                throw new Exception("Storage::put returned false");
            }

            return $path;
        } catch (Exception $e) {
            logger()->error("Error uploading image: " . $e->getMessage());
            if (app()->isLocal()) {
                throw $e;
            }
            return null;
        }
    }
}
