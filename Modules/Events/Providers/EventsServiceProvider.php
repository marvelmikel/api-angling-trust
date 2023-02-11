<?php

namespace Modules\Events\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Events\Console\CheckTicketsHaveBeenGenerated;
use Modules\Events\Console\DeleteAllTicketsForLostEvents;
use Modules\Events\Console\ExportRecentlyCanceledTickets;
use Modules\Events\Entities\Event;

class EventsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->commands([
            CheckTicketsHaveBeenGenerated::class,
            DeleteAllTicketsForLostEvents::class,
            ExportRecentlyCanceledTickets::class
        ]);
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('events.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'events'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/events');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/events';
        }, \Config::get('view.paths')), [$sourcePath]), 'events');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/events');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'events');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'events');
        }
    }

    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public function provides()
    {
        return [];
    }
}
