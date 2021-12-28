<?php

namespace Fcno\LogReader\Exceptions;

use Exception;

/**
 * Parâmetros informados para paginação são inválidos.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://laravel.com/docs/8.x/errors
 */
class InvalidPaginationException extends Exception
{
    protected $message = 'Os valores page e pagination precisam ser maiores ou iguais a 1';
}
