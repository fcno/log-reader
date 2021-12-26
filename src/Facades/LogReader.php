<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\LogReader
 *
 * @link https://laravel.com/docs/8.x/facades
 */
class LogReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'log-reader';
    }
}
