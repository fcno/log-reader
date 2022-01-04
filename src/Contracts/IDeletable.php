<?php

namespace Fcno\LogReader\Contracts;

interface IDeletable extends IReader
{
    /**
     * Deleta o arquivo informado.
     *
     * @param string $log_file Ex.: laravel-2000-12-30.log
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     * @throws \Fcno\LogReader\Exceptions\NotDailyLogException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     *
     * @see https://laravel.com/docs/8.x/logging#configuring-the-single-and-daily-channels
     */
    public function delete(string $log_file): bool;
}
