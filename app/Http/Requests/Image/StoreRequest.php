<?php

namespace App\Http\Requests\Image;

use App\Jobs\Image\StoreImageJob;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
//            'uploaded_images' => 'required|array|max:10',
            'uploaded_image' => 'file',
        ];
    }
    protected function passedValidation() : void
    {
//        foreach ($this->uploaded_images as $image) {
            $filename = md5($this->uploaded_image->getClientOriginalName() . time()) . ".png";
            Storage::disk('public')->putFileAs('images', $this->uploaded_image, $filename);
            $this->replace(['path' => 'images/' . $filename]);
//        }
    }
}
