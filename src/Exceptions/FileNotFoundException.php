<?php

namespace Fcno\LogReader\Exceptions;

use Exception;

/**
 * Arquivo de log diário não existe no ***File System***.
 *
 * @see https://laravel.com/docs/8.x/errors
 */
class FileNotFoundException extends Exception
{
    protected $message = 'O arquivo informado não foi encontrado';
}
