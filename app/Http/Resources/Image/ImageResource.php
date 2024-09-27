<?php

namespace App\Http\Resources\Image;

use App\Http\Resources\ImageTask\ImageTaskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'preview_url' => $this->preview_url,
            'url' => $this->url,
            'task' => [
                'is_fail' => (boolean)$this->task->is_fail
            ]
        ];
    }
}
