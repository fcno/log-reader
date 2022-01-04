<?php

namespace Fcno\LogReader;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * @see https://github.com/spatie/package-skeleton-laravel
 */
class LogReaderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('log-reader');
    }

    public function registeringPackage()
    {
        $this->app->bind('log-reader', function ($app) {
            return new LogReader();
        });

        $this->app->bind('record-reader', function ($app) {
            return new RecordReader();
        });

        $this->app->bind('summary-reader', function ($app) {
            return new SummaryReader();
        });
    }
}
