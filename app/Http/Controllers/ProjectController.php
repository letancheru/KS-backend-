<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProjectRequest  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_category_id' => 'required|exists:project_categories,id',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'project_manager' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $projectData = $request->only([
            'title', 'description', 'project_category_id', 'client_name',
            'client_email', 'project_manager', 'start_date', 'end_date',
            'budget', 'status', 'location', 'notes',
        ]);

        $project = Project::create($projectData);


        if ($request->hasFile('banner')) {

            $bannerPath = $request->file('banner')->store('project_banners', 'public');
            $project->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {


            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('project_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $project->update(['images' => $imagePaths]);
        }

        if ($request->hasFile('attachments')) {

            $attachmentPaths = [];
            foreach ($request->file('attachments') as $attachment) {
                $attachmentPath = $attachment->store('project_attachments', 'public');
                $attachmentPaths[] = $attachmentPath;
            }

            $project->update(['attachments' => $attachmentPaths]);
        }

        return response()->json(['message' => 'Project Created successfully', 'project'=>$project], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $project = Project::with('category')->findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProjectRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function update(Request $request, Project $project): JsonResponse
    {


        $validator = validator($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_category_id' => 'required|exists:project_categories,id',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'project_manager' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $projectData = $request->only([
            'title', 'description', 'project_category_id', 'client_name',
            'client_email', 'project_manager', 'start_date', 'end_date',
            'budget', 'status', 'location', 'notes',
        ]);

        $project->update($projectData);

        if ($request->hasFile('banner')) {
            // Delete the previous banner if it exists
            if ($project->banner) {
                Storage::disk('public')->delete($project->banner);
            }

            $bannerPath = $request->file('banner')->store('project_banners', 'public');
            $project->update(['banner' => $bannerPath]);
        }

        if ($request->hasFile('images')) {
            // Delete the previous images if they exist
            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('project_images', 'public');
                $imagePaths[] = $imagePath;
            }

            $project->update(['images' => $imagePaths]);
        }

        if ($request->hasFile('attachments')) {
            // Delete previous attachments if they exist
            foreach ($project->attachments as $attachment) {
                Storage::disk('public')->delete($attachment);
            }

            $attachmentPaths = [];
            foreach ($request->file('attachments') as $attachment) {
                $attachmentPath = $attachment->store('project_attachments', 'public');
                $attachmentPaths[] = $attachmentPath;
            }

            $project->update(['attachments' => $attachmentPaths]);
        }

        return response()->json(['message' => 'Project Updated successfully', 'project'=>$project], 200);
    }

    public function updateImagesAndBanner(Request $request, $id)
    {

        $project = Project::findOrFail($id);

        // Validate the request
        $validator = validator($request->all(), [
            'images.*' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project = Project::findOrFail($id);

        if ($request->has('images') && $request->input('images')!=null && is_array($request->input('images'))) {
            // Decode and store each image
            $imagePaths = [];
            foreach ($request->input('images') as $base64Image) {
                $decodedImage = base64_decode($base64Image);
                $imagePath = 'project_images/' . uniqid() . '.png';
                Storage::disk('public')->put($imagePath, $decodedImage);
                $imagePaths[] = $imagePath;
            }

            // Update project with the new image paths
            $project->update(['images' => $imagePaths]);
        }

        // Handle base64 banner update
        if ($request->has('banner') && $request->input('banner')!=null) {
            $base64Banner = $request->input('banner');
            $decodedBanner = base64_decode($base64Banner);
            $bannerPath = 'project_banners/' . uniqid() . '.png';
            Storage::disk('public')->put($bannerPath, $decodedBanner);

            // Update project with the new banner path
            $project->update(['banner' => $bannerPath]);
        }

        return response()->json(['message' => 'Images and banner updated successfully', 'project' => $project], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully'], 204);
    }
}