<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|admin');
    }

    public function index()
    {
        $grouped = Setting::grouped();
        return view('settings.index', compact('grouped'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $existing = Setting::where('key', $key)->first();
            $group = $existing ? $existing->group : 'general';
            Setting::set($key, $value ?? '', $group);
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
