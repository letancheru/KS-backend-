<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        return Team::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'title' => 'required|string',
            'position' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $team = new Team([
            'full_name' => $request->input('full_name'),
            'title' => $request->input('title'),
            'position' => $request->input('position'),
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('team_images', 'public');
            $team->image = $imagePath;
        }

        $team->save();

        return response()->json($team, 201);
    }

    public function show($id)
    {
        return Team::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'string',
            'title' => 'string',
            'position' => 'string',
        ]);

        $team = Team::findOrFail($id);

        $team->full_name = $request->input('full_name', $team->full_name);
        $team->title = $request->input('title', $team->title);
        $team->position = $request->input('position', $team->position);

        if ($request->hasFile('image')) {
            if ($team->image) {
                Storage::delete($team->image);
            }
            $imagePath = $request->file('image')->store('team_images');
            $team->image = $imagePath;
        }

        $team->save();

        return response()->json($team, 200);
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);

        if ($team->image) {
            Storage::delete($team->image);
        }

        $team->delete();
        return response()->json(null, 204);
    }

    public function updateImage(Request $request, $id)
    {

        $team = Team::findOrFail($id);

        $validator = validator($request->all(), [
            'image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('image') && $request->input('image')!=null) {
            $base64Image = $request->input('image');
            $decodedImage = base64_decode($base64Image);
            $imagePath = 'team_images/' . uniqid() . '.png';
            Storage::disk('public')->put($imagePath, $decodedImage);

            $team->update(['image' => $imagePath]);
        }

        return response()->json(['message' => 'Team image updated successfully', 'team' => $team], 200);
    }
}
