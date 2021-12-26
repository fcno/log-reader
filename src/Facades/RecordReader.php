<?php

namespace Fcno\LogReader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fcno\LogReader\RecordReader
 *
 * @link https://laravel.com/docs/8.x/facades
 */
class RecordReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'record-reader';
    }
}
