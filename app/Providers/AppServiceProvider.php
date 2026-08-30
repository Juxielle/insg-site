<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\SiteMedia;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrapFive();
        View::composer('*', function ($view): void {
            $request = request();
            if (! $request->attributes->has('siteSettings')) {
                $request->attributes->set('siteSettings', Schema::hasTable('site_settings') ? SiteSetting::pluck('value', 'key') : collect());
            }
            if (! $request->attributes->has('siteMedia')) {
                $request->attributes->set('siteMedia', Schema::hasTable('site_media') ? SiteMedia::pluck('image_url', 'key') : collect());
            }
            $view->with('siteSettings', $request->attributes->get('siteSettings'));
            $view->with('siteMedia', $request->attributes->get('siteMedia'));
        });
    }
}
