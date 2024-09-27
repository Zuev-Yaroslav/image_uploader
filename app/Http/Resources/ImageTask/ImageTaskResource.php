<?php

namespace App\Http\Resources\ImageTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->image->id,
            'url' => $this->image->url,
            'task' => ImageTaskResource::make($this)->resolve()
        ];
    }
}
