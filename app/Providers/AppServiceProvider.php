<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use App\Pdrb;
use App\Observers\PdrbObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        
        // URL::forceRootUrl(config()->get('app.url'));    
        // And this if you wanna handle https URL scheme
        // It's not usefull for http://www.example.com, it's just to make it more independant from the constant value
        // if (str_contains(config()->get('app.url'), 'https://')) {
        //     URL::forceScheme('https');
        // }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Pdrb::observe(PdrbObserver::class);
    }
}
