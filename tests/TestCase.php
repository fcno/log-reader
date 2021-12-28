<?php

namespace Fcno\LogReader\Tests;

use Fcno\LogReader\LogReaderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LogReaderServiceProvider::class,
        ];
    }
}
