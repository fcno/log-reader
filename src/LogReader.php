<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem  $storage
     *
     * File System onde estão armazenados os arquivos de log da aplicação.
     */
    private $file_system;

    /**
     * Define o file system de armazenamento dos logs da aplicação de acordo
     * com o nome informado.
     *
     * @param string  $disk nome do file system
     */
    public function from(string $disk): static
    {
        $this->file_system = Storage::disk($disk);

        return $this;
    }

    /**
     * Sumário de determinado log.
     *
     * Sumariza:
     * - Data do log
     * - Quantidade de registros por level
     *
     * @param string  $log_file Ex.: laravel-2000-12-30.log
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     */
    public function getDailySummary(string $log_file)
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);
    }
}
