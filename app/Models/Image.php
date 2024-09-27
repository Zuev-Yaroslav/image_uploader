<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Builder
 */
class Image extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function getPreviewUrlAttribute() : string
    {
        return Storage::url($this->preview_path);
    }
    public function getUrlAttribute() : string
    {
        return Storage::url($this->path);
    }
    public function deletePreviewImage() : void
    {
        if (Storage::disk('public')->exists($this->preview_path)) {
            Storage::disk('public')->delete($this->preview_path);
        }
    }
    public function task() : HasOne
    {
        return $this->hasOne(ImageTask::class)->withDefault(function () {
            return new ImageTask();
        });
    }
}
