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

    public function uploadImage(UploadedFile $file, string $folder = 'images')
    {
        try {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = "$folder/$filename";

            $image = $this->imageManager->read($file->getPathname())->cover(300, 300);

            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder, 0755, true);
            }

            Storage::disk('public')->put($path, (string)$image->encode());

            return $path;
        } catch (Exception $e) {
            logger()->error("Erro ao fazer upload da imagem: " . $e->getMessage());
            return null;
        }
    }
}
