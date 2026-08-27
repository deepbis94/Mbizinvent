<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function viewSettings()
    {
        $setting = Settings::query()->first();

        return view('pages.settings.detail', compact('setting'));
    }

    public function settingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'dist_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'state_code' => 'required',
            'phone' => 'required',
            'gstin_number' => 'required',
            'pan_number' => 'required',
            'profit_calc' => 'required',
        ]);

        $setting = Settings::query()->where('id', 1)->first();
        if (! $setting) {
            return response()->json(['success' => false, 'message' => 'Settings not found.'], 404);
        }

        $setting->fill($validated);
        $setting->save();

        return response()->json(['success' => true, 'message' => 'Setting updated successfully!']);
    }
}
