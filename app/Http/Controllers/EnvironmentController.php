<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EnvironmentController extends Controller
{
    public function index()
    {
        return view('environment.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'TWILIO_SID' => 'required|string',
            'TWILIO_AUTH_TOKEN' => 'required|string',
            'TWILIO_PHONE_NUMBER' => 'required|string',
            'GEMINI_API_KEY' => 'required|string',
        ]);

        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            $envContent = $this->setEnvValue($envContent, 'TWILIO_SID', $request->TWILIO_SID);
            $envContent = $this->setEnvValue($envContent, 'TWILIO_AUTH_TOKEN', $request->TWILIO_AUTH_TOKEN);
            $envContent = $this->setEnvValue($envContent, 'TWILIO_PHONE_NUMBER', $request->TWILIO_PHONE_NUMBER);
            $envContent = $this->setEnvValue($envContent, 'GEMINI_API_KEY', $request->GEMINI_API_KEY);

            File::put($envPath, $envContent);

            return redirect()->back()->with('status', 'Environment variables updated successfully!');
        } else {
            return redirect()->back()->with('error', '.env file not found!');
        }
    }

    private function setEnvValue($envContent, $key, $value)
    {
        if (strpos($envContent, $key) !== false) {
            $envContent = preg_replace('/^' . $key . '.*/m', $key . '=' . $value, $envContent);
        } else {
            $envContent .= PHP_EOL . $key . '=' . $value;
        }

        return $envContent;
    }
}
