<?php

namespace Fcno\LogReader;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IReader
{
    /**
     * Define o file system de armazenamento dos logs da aplicação de acordo
     * com o nome informado.
     *
     * @param string  $disk nome do file system
     */
    public function from(string $disk): static;

    /**
     * Obtém as informações solicitadas.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection;

    // /**
    //  * Informações sobre um determinado log.
    //  *
    //  * @param string  $log_file
    //  *
    //  * @return static
    //  *
    //  * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
    //  */
    // public function infoAbout(string $log_file): static;

    // /**
    //  * Resultados integrais e sem paginação.
    //  *
    //  * @return \Illuminate\Support\Collection
    //  */
    // public function get(): Collection;

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
