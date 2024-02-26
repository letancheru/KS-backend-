<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $projectCategories = ProjectCategory::all();
        return response()->json($projectCategories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\StoreProjectCategoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator =  validator($request->all(), [
            'name' => 'required|string|max:255|unique:project_categories',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $projectCategory = ProjectCategory::create($request->all());
        return response()->json(['message' => 'Category created successfully', 'projectCategory' => $projectCategory], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProjectCategory  $projectCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProjectCategory $projectCategory)
    {
        return response()->json($projectCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\API\UpdateProjectCategoryRequest  $request
     * @param  \App\Models\ProjectCategory  $projectCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $validator =   validator($request->all(), [
            'name' => 'required|string|max:255|unique:project_categories,name,' . $projectCategory->id,
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $projectCategory->update($request->all());
        return response()->json(['message' => 'Category Updated successfully', 'user'=>$projectCategory], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectCategory  $projectCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        $projectCategory->delete();
        return response()->json(['message' => 'Category Deleted successfully'], 204);
    }
}
