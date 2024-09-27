<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageTask extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function image()
    {
        return $this->belongsTo(Image::class)->withDefault(function () {
            return new Image();
        });
    }
}
