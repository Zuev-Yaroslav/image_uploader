<?php

namespace App\Image;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class ImageUploader
{
    private ImageInterface $manager;
    private string $path;
    private string $previewPath;
    public static function make(string $path): ImageUploader
    {
        return new ImageUploader($path);
    }
    private function __construct(string $path)
    {
        $this->manager = ImageManager::imagick()->read(Storage::disk('public')->get($path));
        $this->path = $path;
    }
    private function uploadOriginalImage() : void
    {
        if ($this->manager->width() > 2000 && $this->manager->width() > $this->manager->height()){
            $height = $this->manager->height() / ($this->manager->width() / 2000);
            $this->manager->resize(2000, $height);
        }
        elseif ($this->manager->height() > 2000) {
            $width = $this->manager->width() / ($this->manager->height() / 2000);
            $this->manager->resize($width, 2000);
        }
        $this->manager
            ->toPng()
            ->save(storage_path('app/public/' . $this->path));
    }
    private function uploadPreviewImage() : void
    {
        $this->manager->cover(400, 600, 'center');
        $this->previewPath = 'images/preview_' . basename($this->path);

        $this->manager
            ->toPng()
            ->save(storage_path('app/public/' . $this->previewPath));
    }
    public function process() : ImageUploader
    {
        $this->uploadOriginalImage();
        $this->uploadPreviewImage();
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPreviewPath(): string
    {
        return $this->previewPath;
    }
}
