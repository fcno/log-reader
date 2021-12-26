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
     * Coleção vazia ou com a quantidade de itens menor que a solicitada se o
     * arquivo já tiver chegado ao final do arquivo.
     *
     * @param int  $page
     * @param int  $per_page
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \RuntimeException
     */
    public function paginate(int $page, int $per_page): Collection;
}
