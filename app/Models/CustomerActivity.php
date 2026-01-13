<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'activity_data' => 'array',
    ];

    /**
     * Get the user for this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a search activity
     */
    public static function logSearch($userId, $searchQuery, $resultCount)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => 'search',
            'activity_data' => [
                'query' => $searchQuery,
                'result_count' => $resultCount,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a product view activity
     */
    public static function logProductView($userId, $productId, $productName, $productCode)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => 'product_view',
            'activity_data' => [
                'product_id' => $productId,
                'product_name' => $productName,
                'product_code' => $productCode,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Log a product modal view activity
     */
    public static function logProductModalView($userId, $productId, $productName, $productCode)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => 'product_modal_view',
            'activity_data' => [
                'product_id' => $productId,
                'product_name' => $productName,
                'product_code' => $productCode,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Log a campaign popup view activity
     */
    public static function logCampaignPopup($userId)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => 'campaign_popup',
            'activity_data' => [
                'viewed_at' => now()->toDateTimeString(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Log a login activity
     */
    public static function logLogin($userId)
    {
        return self::create([
            'user_id' => $userId,
            'activity_type' => 'login',
            'activity_data' => [
                'login_at' => now()->toDateTimeString(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
