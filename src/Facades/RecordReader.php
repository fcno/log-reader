<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\RecordReader
 */
class RecordReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'record-reader';
    }
}
