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

        // Logo yüklemesi - direkt public/storage/settings/ klasörüne kaydet (symlink gerekmez)
        if ($request->hasFile('site_logo')) {
            // Eski logoyu sil
            if ($settings->site_logo) {
                $oldPath = public_path('storage/' . $settings->site_logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Klasör yoksa oluştur
            $uploadPath = public_path('storage/settings');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Dosyayı yükle
            $file = $request->file('site_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $data['site_logo'] = 'settings/' . $filename;
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

    /**
     * Delete the site logo.
     */
    public function deleteLogo()
    {
        $settings = SiteSetting::getSettings();

        if ($settings->site_logo) {
            $logoPath = public_path('storage/' . $settings->site_logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
            $settings->update(['site_logo' => null]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Logo başarıyla silindi.');
    }
}
