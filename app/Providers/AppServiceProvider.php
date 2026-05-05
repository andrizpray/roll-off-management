<?php

namespace App\Providers;

use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer — unassignedCount with 60s cache
        View::composer('layouts.app', function ($view) {
            if (!Schema::hasTable('delivery_orders')) {
                $view->with('unassignedCount', 0);
                return;
            }

            $count = Cache::remember('unassigned_do_count', 60, function () {
                return DeliveryOrder::whereDoesntHave('assignments')
                    ->whereNotIn('status', ['delivered', 'cancelled'])
                    ->count();
            });

            $view->with('unassignedCount', $count);
        });
    }
}
