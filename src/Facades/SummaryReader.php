<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\SummaryReader
 * @see https://laravel.com/docs/8.x/facades
 */
class SummaryReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'summary-reader';
    }
}
