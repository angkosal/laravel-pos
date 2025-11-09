<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        if (! $this->app->runningInConsole()) {
            // 'key' => 'value'
            $settings = Setting::all('key', 'value')
                ->keyBy('key')
                ->transform(fn($setting) => $setting->value)
                ->toArray();
            config([
               'settings' => $settings
            ]);

            config(['app.name' => config('settings.app_name')]);
        }

        Paginator::useBootstrap();
    }
}
