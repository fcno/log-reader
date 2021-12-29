<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IPaginate
{
    /**
     * Resultados paginados.
     *
     * Coleção vazia ou com a quantidade de itens menor que a solicitada se já
     * tiver chegado ao final.
     *
     * @throws \Fcno\LogReader\Exceptions\InvalidPaginationException `$page < 1 || $per_page < 1`
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     */
    public function paginate(int $page, int $per_page): Collection;
}
