<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // MYSQL Version Fix
        Schema::defaultStringLength(191);

        // Laravel Passport
        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(90));
        Passport::personalAccessTokensExpireIn(now()->addDays(30));

        // Helper Functions
        require_once __DIR__ . '/../helpers.php';
    }
}
