<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Support\Collection;

interface IReadable extends IReader
{
    /**
     * Obtém as informações solicitadas.
     *
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     */
    public function get(): Collection;
}
