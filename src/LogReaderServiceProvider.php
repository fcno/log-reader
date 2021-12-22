<?php

namespace Fcno\LogReader;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Fcno\LogReader\Commands\LogReaderCommand;

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
}
