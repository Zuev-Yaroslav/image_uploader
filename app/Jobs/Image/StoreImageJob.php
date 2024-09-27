<?php

namespace App\Jobs\Image;

use App\Image\ImageUploader;
use App\Models\Image;
use App\Models\ImageTask;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class StoreImageJob implements ShouldQueue
{
    use Queueable;
    private ?Image $image = null;

    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(private string $path)
    {

    }

    /**
     * Execute the job.
     */
    public function handle() : void
    {
        try {
            DB::beginTransaction();
            $imageUploader = ImageUploader::make($this->path)->process();

            $this->image = Image::create([
                'path' => $imageUploader->getPath(),
                'preview_path' => $imageUploader->getPreviewPath()
            ]);
            $this->image->task()->create([
                'is_fail' => false
            ]);
            DB::commit();
        }
        catch (Exception $exception) {
            DB::rollBack();
            $this->image = Image::create();
            $this->image->task()->create(['exception' => $exception]);
            if (Storage::disk('public')->exists($this->path)) {
                Storage::disk('public')->delete($this->path);
            }
        }
    }
}
