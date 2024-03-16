<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;

class BusinessSettingController extends Controller
{

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone1' => 'nullable|string',
            'phone2' => 'nullable|string',
            'telegramLink' => 'nullable|string',
            'facebookLink' => 'nullable|string',
            'instagramLink' => 'nullable|string',
            'linkedInLink' => 'nullable|string',
            'twitterLink' => 'nullable|string',
            'tiktokLink' => 'nullable|string',
            'gmapUrl'=>'nullable|string',
        ]);

        $existingSetting = BusinessSetting::first();

        if ($existingSetting) {
            $existingSetting->update($validatedData);
            $message = 'Business settings updated successfully';
        } else {
            BusinessSetting::Create([
                'address' => $validatedData['address'],
                'email' => $validatedData['email'],
                'phone1' => $validatedData['phone1'],
                'phone2' => $validatedData['phone2'],
                'telegramLink' => $validatedData['telegramLink'],
                'facebookLink' => $validatedData['facebookLink'],
                'instagramLink' => $validatedData['instagramLink'],
                'linkedInLink' => $validatedData['linkedInLink'],
                'twitterLink' => $validatedData['twitterLink'],
                'tiktokLink' => $validatedData['tiktokLink'],
                'gmapUrl' => $validatedData['gmapUrl'],
            ]);
            $message = 'Business settings saved successfully';
        }

        return response()->json(['message' => $message]);
    }


    public function index()
    {
        $businessSetting = BusinessSetting::first();

        if ($businessSetting) {
            return response()->json($businessSetting);
        } else {
            return response()->json(['message' => 'Mail configuration not found'], 404);
        }
    }
}
