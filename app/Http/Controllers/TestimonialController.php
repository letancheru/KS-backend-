<?php

namespace App\Http\Controllers;
use App\Models\TestimonialModel;
use App\Http\Requests\StoreTestimonialRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonisl = TestimonialModel::latest()->get();
        return response()->json($testimonisl)->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTestimonialRequest $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $testimonislData = $request->only([
            'name', 'description',
        ]);
        $testimonisl = TestimonialModel::create($testimonislData);

        if ($request->hasFile('banner')) {

            $bannerPath = $request->file('banner')->store('testimonials_banners', 'public');
            $testimonisl->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('testimonial_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $testimonisl->update(['images' => $imagePaths]);
        }

        return response()->json(['message' => 'testimonisl Added successfully', 'testimonisl'=>$testimonisl], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  TestimonialModel  $testimonisl
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $testimonisl = TestimonialModel::findOrFail($id);
        if($testimonisl){
            return response()->json($testimonisl,200);
        }else{
            return response()->json([],200 );
        }
    }

    /**
     * Update the specified resource in storage.
     *  @param  StoreTestimonialRequest  $request
     * @param  TestimonialModel  $testimonisl
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       $testimonisl = TestimonialModel::findOrFail($id);

       $testimonisl->name = $request->name;
       $testimonisl->description=$request->description;
       $testimonisl->save();



        if ($request->hasFile('banner')) {
            // Delete the previous banner if it exists
            if ( $testimonisl->banner) {
                Storage::disk('public')->delete($testimonisl->banner);
            }

            $bannerPath = $request->file('banner')->store('testimonial_banners', 'public');
            $testimonisl->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {
            // Delete the previous images if they exist
            foreach ($testimonisl->images as $image) {
                Storage::disk('public')->delete($image);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('testimonial_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $testimonisl->update(['images' => $imagePaths]);
        }

        return response()->json(['message' => 'testimonisl Updated successfully', 'testimonisl'=>$testimonisl], 200);
    }

    public function updateImagesAndBanner(Request $request, $id)
    {

        $testimonisl = TestimonialModel::findOrFail($id);

        // Validate the request
        $validator = validator($request->all(), [
            'images.*' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $testimonisl = TestimonialModel::findOrFail($id);

        if ($request->has('images') && $request->input('images')!=null && is_array($request->input('images'))) {
            // Decode and store each image
            $imagePaths = [];
            foreach ($request->input('images') as $base64Image) {
                $decodedImage = base64_decode($base64Image);
                $imagePath = 'testimonial_images/' . uniqid() . '.png';
                Storage::disk('public')->put($imagePath, $decodedImage);
                $imagePaths[] = $imagePath;
            }


            $testimonisl->update(['images' => $imagePaths]);
        }

        // Handle base64 banner update
        if ($request->has('banner') && $request->input('banner')!=null) {
            $base64Banner = $request->input('banner');
            $decodedBanner = base64_decode($base64Banner);
            $bannerPath = 'testimonial_banners/' . uniqid() . '.png';
            Storage::disk('public')->put($bannerPath, $decodedBanner);

            // Update project with the new banner path
            $testimonisl->update(['banner' => $bannerPath]);
        }

        return response()->json(['message' => 'Images and banner updated successfully', 'testimonisl' => $testimonisl], 200);
    }
    /**



     * Remove the specified resource from storage.
     *
     * @param  TestimonialModel  $testimonisl
     * @return JsonResponse
     */
    public function destroy($id):JsonResponse
    {

        $testimonisl = TestimonialModel::findOrFail($id);
        if($testimonisl){
            $testimonisl->delete();
            return response()->json(['message'=>'Deleted Successfully!'],204);
        }
        else{
            return response()->json( ['message'=>'Testimonials Not Found'], 400);
        }
        //return response()->json(['message' => 'testimonisl deleted successfully'], 204);

    } //
}
