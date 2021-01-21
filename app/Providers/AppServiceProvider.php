<?php

namespace App\Providers;

use App\Bedrijf;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Snelstart\Snelstart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Snelstart::class, function() {
            return new Snelstart();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('nl');

        Cache::add('Bedrijven', Bedrijf::all(), 3600 * 8);

        View::composer(
            'layouts.partials.nav',
            function ($view) {
                $view->with(
                    [
                        'bedrijven' => Cache::get('Bedrijven')
                    ]
                );
            }
        );
    }
}
