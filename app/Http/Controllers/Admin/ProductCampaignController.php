<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCampaign;
use Illuminate\Http\Request;

class ProductCampaignController extends Controller
{
    /**
     * Display campaigns for a product
     */
    public function index(Product $product)
    {
        $campaigns = $product->productCampaigns()->orderBy('sira')->get();
        return view('admin.products.campaigns.index', compact('product', 'campaigns'));
    }

    /**
     * Show form to create a new campaign
     */
    public function create(Product $product)
    {
        return view('admin.products.campaigns.create', compact('product'));
    }

    /**
     * Store a new campaign
     */
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'vade_gun' => 'required|integer|min:0',
            'mal_fazlasi_adet' => 'required|integer|min:1',
            'mal_fazlasi' => 'required|integer|min:0',
            'net_fiyat' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sira' => 'required|integer|min:0',
        ]);

        $data['product_id'] = $product->id;
        $data['is_active'] = $request->has('is_active');

        ProductCampaign::create($data);

        return redirect()->route('admin.products.campaigns.index', $product)
            ->with('success', 'Kampanya oluşturuldu.');
    }

    /**
     * Show form to edit a campaign
     */
    public function edit(Product $product, ProductCampaign $campaign)
    {
        return view('admin.products.campaigns.edit', compact('product', 'campaign'));
    }

    /**
     * Update a campaign
     */
    public function update(Request $request, Product $product, ProductCampaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'vade_gun' => 'required|integer|min:0',
            'mal_fazlasi_adet' => 'required|integer|min:1',
            'mal_fazlasi' => 'required|integer|min:0',
            'net_fiyat' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sira' => 'required|integer|min:0',
        ]);

        $data['is_active'] = $request->has('is_active');

        $campaign->update($data);

        return redirect()->route('admin.products.campaigns.index', $product)
            ->with('success', 'Kampanya güncellendi.');
    }

    /**
     * Delete a campaign
     */
    public function destroy(Product $product, ProductCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('admin.products.campaigns.index', $product)
            ->with('success', 'Kampanya silindi.');
    }
}


















