<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\LogReader
 */
class LogReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'log-reader';
    }
}
