<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('APP_ENV') != 'local') {
            \URL::forceScheme('https');
        }
        //prevent error string length
        Schema::defaultStringLength(191);
        
        // Paginate using bootstrap
        Paginator::useBootstrap();
    }
}
