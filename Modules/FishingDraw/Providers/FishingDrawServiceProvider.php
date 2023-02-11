<?php

namespace Modules\FishingDraw\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\FishingDraw\Console\SelectWinners;

class FishingDrawServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('FishingDraw', 'Database/Migrations'));
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->commands([
            SelectWinners::class
        ]);
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path('FishingDraw', 'Config/config.php') => config_path('fishingdraw.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('FishingDraw', 'Config/config.php'), 'fishingdraw'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/fishingdraw');

        $sourcePath = module_path('FishingDraw', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/fishingdraw';
        }, \Config::get('view.paths')), [$sourcePath]), 'fishingdraw');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/fishingdraw');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'fishingdraw');
        } else {
            $this->loadTranslationsFrom(module_path('FishingDraw', 'Resources/lang'), 'fishingdraw');
        }
    }

    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('FishingDraw', 'Database/factories'));
        }
    }

    public function provides()
    {
        return [];
    }
}
