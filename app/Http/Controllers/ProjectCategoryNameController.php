<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $projectCategories = ProjectCategory::all();

        $data = [];
        foreach ($projectCategories as $category) {
            $data[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProjectCategory  $projectCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProjectCategory $projectCategory)
    {
        $data = [
            'id' => $projectCategory->id,
            'name' => $projectCategory->name,
        ];

        return response()->json($data);
    }

    // Other methods remain unchanged...
}
