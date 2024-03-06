<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Partner;
class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::all();
        return response()->json($partners);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the allowed image types and size
        ]);

        $bannerPath = $request->file('banner')->store('partners', 'public'); // Adjust the storage path

        $partner = new Partner;
        $partner->name = $request->input('name');
        $partner->banner = $bannerPath;

        $partner->save();

        return response()->json(['message' => 'Partner created successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'banner' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the allowed image types and size
        ]);

        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(['error' => 'Partner not found'], 404);
        }

        $partner->name = $request->input('name');

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('partners', 'public'); // Adjust the storage path
            $partner->banner = $bannerPath;
        }


        $partner->save();

        return response()->json(['message' => 'Partner updated successfully']);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(['error' => 'Partner not found'], 404);
        }

        if ($partner->banner) {
            Storage::disk('public')->delete($partner->banner);
        }

        $partner->delete();

        return response()->json(['message' => 'Partner deleted successfully']);
    }
}
