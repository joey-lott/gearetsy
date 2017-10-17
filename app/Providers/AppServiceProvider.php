<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      // Register EtsyAPI as a singleton. Honestly, this may not
      // be necessary. It may not need to be instantiated at all.
      // Perhaps all methods should be made static in a future
      // refactor.
      \App::singleton("\App\Etsy\EtsyAPI", function() {
        return new \App\Etsy\EtsyAPI(env('API_KEY'), env('API_SECRET'));
      });
    }
}
