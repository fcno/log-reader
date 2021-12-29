<?php

namespace Fcno\LogReader\Exceptions;

use Exception;

/**
 * Arquivo informado não respeita o padrão de log diário do laravel, isto é, o
 * padrão de nome `laravel-yyyy-mm-dd.log`.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://laravel.com/docs/8.x/errors
 */
class NotDailyLogException extends Exception
{
    protected $message = 'Arquivo com nome inválido. Padrão aceito: laravel-yyyy-mm-dd.log';
}
