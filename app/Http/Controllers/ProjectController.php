<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'status' => 'nullable|in:pending,in_progress,completed',
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
    public function show(Project $project): JsonResponse
    {
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
            'status' => 'nullable|in:pending,in_progress,completed',
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