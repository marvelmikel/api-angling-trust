<?php

namespace Modules\Store\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Auth\Entities\User;
use Modules\Store\Console\AuditSmartDebitUpdate;
use Modules\Store\Console\CheckLinkedSmartDebitMembers;
use Modules\Store\Console\ConvertStripeSubscriptionPrice;
use Modules\Store\Console\FixMissingSmartDebitFrequency;
use Modules\Store\Console\LinkImportedSmartDebitMembers;
use Modules\Store\Console\LinkNewSmartDebitMembers;
use Modules\Store\Console\LinkSmartDebitMembersFromImport;
use Modules\Store\Console\RemoveProrationLineItems;
use Modules\Store\Console\SetFisheryCats;
use Modules\Store\Console\UpdateSmartDebitPayments;
use Modules\Store\Console\UpdateSmartDebitMemberships;
use Modules\Store\Console\UpdateSmartDebitPrices;
use Modules\Store\Console\UpdateStripeSubscriptionPrices;
use Modules\Store\Observers\UserObserver;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        User::observe(UserObserver::class);

        $this->commands([
            UpdateSmartDebitPayments::class,
            LinkImportedSmartDebitMembers::class,
            LinkSmartDebitMembersFromImport::class,
            CheckLinkedSmartDebitMembers::class,
            LinkNewSmartDebitMembers::class,
            UpdateSmartDebitMemberships::class,
            FixMissingSmartDebitFrequency::class,
            AuditSmartDebitUpdate::class,
            ConvertStripeSubscriptionPrice::class,
            SetFisheryCats::class,
            UpdateSmartDebitPrices::class,
            UpdateStripeSubscriptionPrices::class,
            RemoveProrationLineItems::class,
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('store.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'store'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/store');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/store';
        }, \Config::get('view.paths')), [$sourcePath]), 'store');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/store');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'store');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'store');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
