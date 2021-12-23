<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Commands\LogReaderCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://github.com/spatie/package-skeleton-laravel
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
            ->name('log-reader')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_log-reader_table')
            ->hasCommand(LogReaderCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->bind('log-reader', function ($app) {
            return new LogReader();
        });
    }
}
