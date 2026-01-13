<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates
        Gate::define('admin-access', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('plasiyer-access', function ($user) {
            return $user->isPlasiyer();
        });

        Gate::define('musteri-access', function ($user) {
            return $user->isMusteri();
        });
    }
}




















