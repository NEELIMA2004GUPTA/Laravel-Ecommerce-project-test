<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController; 

class ProductReviewController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Product $product)
    {
        return view('reviews.add', compact('product'));
    }

    public function store(Request $request ,Product $product){
        $validator=Validator::make($request->all(),[
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'images.*' => 'nullable|image|max:5120',   
            'video' => 'nullable|mimetypes:video/webm,video/mp4,video/ogg|max:51200' 
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('reviews/images', 'public');
                $review->media()->create([
                    'type' => 'image',
                    'path' => $path,
                    'mime' => $img->getClientMimeType(),
                    'size' => $img->getSize(),
                ]);
            }
        }

        if ($request->hasFile('video')) {
            $vid = $request->file('video');
            $path = $vid->store('reviews/videos', 'public');
            $review->media()->create([
                'type' => 'video',
                'path' => $path,
                'mime' => $vid->getClientMimeType(),
                'size' => $vid->getSize(),
            ]);
        }

        return redirect()->route('product.show', $product->slug)->with('success','Your review has been posted.');
    }

    public function uploadVideo(Request $request, Product $product)
    {
        if (!$request->hasFile('video')) {
            return response()->json(['success' => false, 'message' => 'No video file']);
        }

        $vid = $request->file('video');
        $path = $vid->store('reviews/videos', 'public');

        // Store ONLY the video path in session temporarily
        session()->put('temp_review_video', $path);

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded and saved temporarily '
        ]);
    }
}


    
