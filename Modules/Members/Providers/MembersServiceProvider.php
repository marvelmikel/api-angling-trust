<?php

namespace Modules\Members\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Members\Console\AddAdditionalContactToOrgMembers;
use Modules\Members\Console\AutoRenewMembers;
use Modules\Members\Console\CalculateSmartDebitExpiryDate;
use Modules\Members\Console\CheckExpiryDateDrift;
use Modules\Members\Console\CheckForImReferenceConflicts;
use Modules\Members\Console\CheckForOutOfSyncStripeSubscriptions;
use Modules\Members\Console\ClearAllMemberData;
use Modules\Members\Console\ConnectImportedStripeMembers;
use Modules\Members\Console\ConvertAdultMembersToSeniorCitizens;
use Modules\Members\Console\ConvertOldTradeMembersToNewCategories;
use Modules\Members\Console\CreateOnboardRecordsForAllMembers;
use Modules\Members\Console\DeleteMembersByMembershipNumber;
use Modules\Members\Console\ExportTradeMembersWithRecurringPayments;
use Modules\Members\Console\FixExpiryDateDrift;
use Modules\Members\Console\FixFederationMembersReference;
use Modules\Members\Console\FixOutOfSyncStripeMembers;
use Modules\Members\Console\ImportIMMembers;
use Modules\Members\Console\ImportMemberNumbersToOrgMembers;
use Modules\Members\Console\ImportOrgMembers;
use Modules\Members\Console\NotifyMembersOfExpiringCards;
use Modules\Members\Console\OptInAllImportedMembersToGambling;
use Modules\Members\Console\PopulateMemberSelectOptions;
use Modules\Members\Console\RebuildMembersIndex;
use Modules\Members\Console\ReimportOrgPreferences;
use Modules\Members\Console\ReindexMembersUpdatedSince;
use Modules\Members\Console\RemoveDeletedMembersFromMembersIndex;
use Modules\Members\Console\RepairMembersIndex;
use Modules\Members\Console\SendDataToWarehouse;
use Modules\Members\Console\SendOnboardRecords;
use Modules\Members\Console\UpdateCardExpiry;
use Modules\Members\Console\UpdateFullNameOfAllMembers;
use Modules\Members\Console\UpdateFullNameOfMembers;
use Modules\Members\Console\UpdateIMMemberRecordsFromImport;
use Modules\Members\Console\UpdateORGMemberRecordsFromImport;
use Modules\Members\Console\MembershipAgeFix;
use Modules\Members\Entities\Member;
use Modules\Members\Observers\MemberObserver;

class MembersServiceProvider extends ServiceProvider
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

        $this->commands([
            AutoRenewMembers::class,
            PopulateMemberSelectOptions::class,
            SendDataToWarehouse::class,
            ImportIMMembers::class,
            ClearAllMemberData::class,
            ImportOrgMembers::class,
            CreateOnboardRecordsForAllMembers::class,
            SendOnboardRecords::class,
            AddAdditionalContactToOrgMembers::class,
            FixFederationMembersReference::class,
            ImportMemberNumbersToOrgMembers::class,
            ReimportOrgPreferences::class,
            UpdateFullNameOfAllMembers::class,
            OptInAllImportedMembersToGambling::class,
            CheckForImReferenceConflicts::class,
            UpdateFullNameOfMembers::class,
            UpdateIMMemberRecordsFromImport::class,
            UpdateORGMemberRecordsFromImport::class,
            ConvertAdultMembersToSeniorCitizens::class,
            RebuildMembersIndex::class,
            RemoveDeletedMembersFromMembersIndex::class,
            ReindexMembersUpdatedSince::class,
            UpdateCardExpiry::class,
            ReindexMembersUpdatedSince::class,
            CalculateSmartDebitExpiryDate::class,
            DeleteMembersByMembershipNumber::class,
            NotifyMembersOfExpiringCards::class,
            ConvertOldTradeMembersToNewCategories::class,
            ExportTradeMembersWithRecurringPayments::class,
            ConnectImportedStripeMembers::class,
            RepairMembersIndex::class,
            CheckForOutOfSyncStripeSubscriptions::class,
            FixOutOfSyncStripeMembers::class,
            FixExpiryDateDrift::class,
            CheckExpiryDateDrift::class,
            MembershipAgeFix::class,
        ]);

        Member::observe(MemberObserver::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('members.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'members'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/members');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/members';
        }, \Config::get('view.paths')), [$sourcePath]), 'members');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/members');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'members');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'members');
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
