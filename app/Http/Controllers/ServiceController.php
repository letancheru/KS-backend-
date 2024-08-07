<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $service = Service::latest()->get();
        return response()->json($service)->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreServiceRequest  $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        $validator = validator($request->all(), [
            'serviceName' => 'required|string|max:255',
            'description' => 'string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $serviceData = $request->only([
            'serviceName', 'description',
        ]);
        $service = Service::create($serviceData);

        if ($request->hasFile('banner')) {

            $bannerPath = $request->file('banner')->store('service_banners', 'public');
            $service->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('service_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $service->update(['images' => $imagePaths]);
        }

        return response()->json(['message' => 'Service Added successfully', 'service'=>$service], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Service  $service
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $service = Service::findOrFail($id);
        if($service){
            return response()->json($service,200);
        }else{
            return response()->json([],200 );
        }
    }

    /**
     * Update the specified resource in storage.
     *  @param  UpdateServiceRequest  $request
     * @param  Service  $service
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = validator($request->all(), [
            'serviceName' => 'required|string|max:255',
            'description' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       $service = Service::findOrFail($id);

       $service->serviceName = $request->serviceName;
       $service->description=$request->description;
       $service->save();

      

        if ($request->hasFile('banner')) {
            // Delete the previous banner if it exists
            if ( $service->banner) {
                Storage::disk('public')->delete($service->banner);
            }

            $bannerPath = $request->file('banner')->store('service_banners', 'public');
            $service->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {
            // Delete the previous images if they exist
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('service_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $service->update(['images' => $imagePaths]);
        }

        return response()->json(['message' => 'service Updated successfully', 'service'=>$service], 200);
    }

    public function updateImagesAndBanner(Request $request, $id)
    {

        $service = Service::findOrFail($id);

        // Validate the request
        $validator = validator($request->all(), [
            'images.*' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::findOrFail($id);

        if ($request->has('images') && $request->input('images')!=null && is_array($request->input('images'))) {
            // Decode and store each image
            $imagePaths = [];
            foreach ($request->input('images') as $base64Image) {
                $decodedImage = base64_decode($base64Image);
                $imagePath = 'service_images/' . uniqid() . '.png';
                Storage::disk('public')->put($imagePath, $decodedImage);
                $imagePaths[] = $imagePath;
            }


            $service->update(['images' => $imagePaths]);
        }

        // Handle base64 banner update
        if ($request->has('banner') && $request->input('banner')!=null) {
            $base64Banner = $request->input('banner');
            $decodedBanner = base64_decode($base64Banner);
            $bannerPath = 'service_banners/' . uniqid() . '.png';
            Storage::disk('public')->put($bannerPath, $decodedBanner);

            // Update project with the new banner path
            $service->update(['banner' => $bannerPath]);
        }

        return response()->json(['message' => 'Images and banner updated successfully', 'service' => $service], 200);
    }
    /**



     * Remove the specified resource from storage.
     *
     * @param  Service  $service
     * @return JsonResponse
     */
    public function destroy($id):JsonResponse
    {

        $service = Service::findOrFail($id);
        if($service){
            $service->delete();
            return response()->json(['message'=>'Deleted Successfully!'],204);
        }
        else{
            return response()->json( ['message'=>'Service Not Found'], 400);
        }
        //return response()->json(['message' => 'Service deleted successfully'], 204);

    }
}
