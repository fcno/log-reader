<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IReadable extends IReader
{
    /**
     * Obtém as informações solicitadas.
     *
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     */
    public function get(): Collection;
}
