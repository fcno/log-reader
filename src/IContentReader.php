<?php

namespace Fcno\LogReader;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IContentReader extends IReader
{
    /**
     * Define o arquivo de log que será trabalhado.
     *
     * @param string  $log_file Ex.: laravel-2000-12-30.log
     *
     * @return static
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     */
    public function infoAbout(string $log_file): static;

    // /**
    //  * Resultados paginados.
    //  *
    //  * Coleção vazia ou com a quantidade de itens menor que a solicitada se o
    //  * arquivo já tiver chegado ao final do arquivo.
    //  *
    //  * @param int  $page
    //  * @param int  $per_page
    //  *
    //  * @return \Illuminate\Support\Collection
    //  *
    //  * @throws \RuntimeException
    //  */
    // public function paginate(int $page, int $per_page): Collection;
}
