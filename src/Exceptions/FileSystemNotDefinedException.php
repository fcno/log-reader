<?php

namespace Fcno\LogReader\Exceptions;

use Exception;

/**
 * Não foi definido o ***File System*** antes de inicar a manipulação do
 * arquivo de log.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://laravel.com/docs/8.x/errors
 */
class FileSystemNotDefinedException extends Exception
{
    protected $message = 'O File System deve ser definido antes da manipulação do arquivo de log';
}
