<?php

namespace Fcno\LogReader;

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
}
