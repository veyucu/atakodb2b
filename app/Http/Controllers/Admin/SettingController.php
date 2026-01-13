<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function edit()
    {
        $setting = Setting::first();

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $setting = Setting::firstOrCreate([]);

        if ($request->hasFile('site_logo')) {
            if ($setting->site_logo) {
                Storage::disk('public')->delete($setting->site_logo);
            }

            $data['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }

        $setting->update($data);

        Cache::forget('site_settings');

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Site ayarları güncellendi.');
    }
}




















