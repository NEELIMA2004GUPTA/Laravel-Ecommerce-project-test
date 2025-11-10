<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController; 
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;

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
            'images' => 'nullable|array|max:5',
            // Conditional size validation
            'images.*' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,jfif,mp4,webm,ogg',
                // Image files → max 1MB (1024 KB)
                Rule::when(
                    fn ($value) => $value instanceof UploadedFile && str_starts_with($value->getMimeType(), 'image/'),
                    'max:1024'
                ),

                // Video files → max 5MB (5120 KB)
                Rule::when(
                    fn ($value) => $value instanceof UploadedFile && str_starts_with($value->getMimeType(), 'video/'),
                    'max:5120'
                ),

            ],
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
            foreach ($request->file('images') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $size = $file->getSize();

                // Image Size Limit: 1MB
                if (in_array($extension, ['jpg','jpeg','png','webp','jfif'])) {
                    if ($size > 1024 * 1024) {
                        return back()->withErrors(['images' => 'Each image must be less than 1MB']);
                    }
                    $type = 'image';
                }

                // Video Size Limit: 25MB
                elseif (in_array($extension, ['mp4','webm','ogg'])) {
                    if ($size > (25 * 1024 * 1024)) {
                        return back()->withErrors(['images' => 'Each video must be less than 25MB']);
                    }
                    $type = 'video';
                }

                // Any other file type
                else {
                    return back()->withErrors(['images' => 'Invalid file format.']);
                }

                // Store File
                $path = $file->store('reviews/media', 'public');

                // Save to DB
                $review->media()->create([
                    'type' => $type,  // image or video
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'size' => $size,
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

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $product = $review->product; 

        // Check if the current user is the owner of the review
        if ($review->user_id !== auth()->id()) {
            return redirect()->route('product.show',  $product->slug)->with('error', 'You are not authorized to delete this review.');
        }

        // Delete the review
        $review->delete();

        return redirect()->route('product.show',  $product->slug)->with('success', 'Review deleted successfully.');
    }
}


    
