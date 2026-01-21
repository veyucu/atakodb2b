<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function index()
    {
        $settings = SiteSetting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'gonderim_sekilleri' => 'nullable|array',
            'gonderim_sekilleri.*.aciklama' => 'nullable|string|max:100',
            'gonderim_sekilleri.*.erp_aciklama' => 'nullable|string|max:20',
        ]);

        $settings = SiteSetting::getSettings();

        // Logo yüklemesi
        if ($request->hasFile('site_logo')) {
            // Eski logoyu sil
            if ($settings->site_logo) {
                \Storage::disk('public')->delete($settings->site_logo);
            }

            $data['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }

        // Gönderim şekillerini temizle (boş olanları kaldır)
        if (isset($data['gonderim_sekilleri'])) {
            $data['gonderim_sekilleri'] = array_values(array_filter($data['gonderim_sekilleri'], function ($item) {
                return !empty($item['aciklama']) || !empty($item['erp_aciklama']);
            }));
        }

        $settings->update($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Site ayarları başarıyla güncellendi.');
    }
}

