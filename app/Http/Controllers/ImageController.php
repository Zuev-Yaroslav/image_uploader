<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\StoreRequest;
use App\Http\Resources\Image\ImageResource;
use App\Jobs\Image\StoreImageJob;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $images = ImageResource::collection(Image::latest()->get());
        return ($request->wantsJson())
            ? $images
            : inertia('Image/Index', compact('images'));
    }
    public function store(StoreRequest $request)
    {
        $data = $request->validationData();
        StoreImageJob::dispatch($data['path']);
        return response([], Response::HTTP_OK);
    }
//    public function indexImageTask()
//    {
//        $images = ImageTaskResource::collection(ImageTask::latest()->get());
//        return $images;
//    }
}
