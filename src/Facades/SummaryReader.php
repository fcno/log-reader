<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\SummaryReader
 */
class SummaryReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'summary-reader';
    }
}
