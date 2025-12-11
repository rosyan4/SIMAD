<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AssetCodeService;
use App\Services\AssetService;
use App\Services\AssetValidatorService;
use App\Services\CategoryService;
use App\Services\AssetDeletionService;
use App\Services\AssetMutationService;
use App\Services\DashboardService;
use App\Services\ReportService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AssetCodeService::class, function ($app) {
            return new AssetCodeService();
        });

        $this->app->singleton(AssetValidatorService::class, function ($app) {
            return new AssetValidatorService();
        });

        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        $this->app->singleton(AssetDeletionService::class, function ($app) {
            return new AssetDeletionService();
        });

        $this->app->singleton(AssetMutationService::class, function ($app) {
            return new AssetMutationService();
        });

        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(AssetService::class),
                $app->make(AssetDeletionService::class),
                $app->make(AssetMutationService::class)
            );
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });

        $this->app->singleton(AssetService::class, function ($app) {
            return new AssetService(
                $app->make(AssetCodeService::class),
                $app->make(AssetValidatorService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}