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

            tenancy()->end();

            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder, 0755, true);
            }

            Storage::disk('public')->put($path, (string)$image->encode());

            $a = tenancy()->initialize(auth()->user()->tenant);

            //dd($a);

            return $path;
        } catch (Exception $e) {
            logger()->error("Erro ao fazer upload da imagem: " . $e->getMessage());
            return null;
        }
    }
}
