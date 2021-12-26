<?php

namespace Fcno\LogReader\Contracts;

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
}
