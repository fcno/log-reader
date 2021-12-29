<?php

namespace Fcno\LogReader\Contracts;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IContentReader extends IReader
{
    /**
     * Define o arquivo de log diário que será trabalhado.
     *
     * @param string $log_file Ex.: laravel-2000-12-30.log
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     * @throws \Fcno\LogReader\Exceptions\NotDailyLogException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     *
     * @see https://laravel.com/docs/8.x/logging#configuring-the-single-and-daily-channels
     */
    public function infoAbout(string $log_file): static;
}
