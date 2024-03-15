<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailConfig;

class MailConfigController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'mailerName' => 'required',
            'host' => 'required',
            'driver' => 'required',
            'port' => 'required|numeric',
            'username' => 'required',
            'email_id' => 'required|email',
            'encryption' => 'required',
            'password' => 'required',
            'admin_email'=>'required|email'
        ]);


        $existingConfig = MailConfig::first();

        if ($existingConfig) {
            $existingConfig->update($validatedData);
            $message = 'Mail configuration updated successfully';
        } else {
            MailConfig::create([
                'mailerName' => $validatedData['mailerName'],
                'host' => $validatedData['host'],
                'driver' => $validatedData['driver'],
                'port' => $validatedData['port'],
                'username' => $validatedData['username'],
                'email_id' => $validatedData['email_id'],
                'encryption' => $validatedData['encryption'],
                'password' => $validatedData['password'],
                'admin_email' => $validatedData['admin_email']
            ]);

            $message = 'Mail configuration saved successfully';
        }

        return response()->json(['message' => $message]);
    }

    public function getMailConfig()
    {
        $mailConfig = MailConfig::first();

        if ($mailConfig) {
            return response()->json($mailConfig);
        } else {
            return response()->json(['message' => 'Mail configuration not found'], 404);
        }
    }
}
