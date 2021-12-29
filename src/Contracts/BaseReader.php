<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
abstract class BaseReader implements IReader, IReadable
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     *
     * ***File System*** onde estão armazenados os arquivos de log diário da
     * aplicação
     */
    protected Filesystem $file_system;

    /**
     * {@inheritdoc}
     */
    public function from(string $disk): static
    {
        $this->file_system = Storage::disk($disk);

        return $this;
    }
}
