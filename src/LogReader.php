<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Contracts\IReader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Manipular os arquivos de log do file system.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader implements IReader
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     *
     * File System onde estão armazenados os arquivos de log da aplicação.
     */
    private $file_system;

    /**
     * @inheritdoc
     */
    public function from(string $disk): static
    {
        $this->file_system = Storage::disk($disk);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * Nesse caso, os arquivos de log
     */
    public function get(): Collection
    {
        return collect($this->file_system->files())
                ->sortDesc()
                ->values();
    }

    /**
     * Arquivos de log do file system ordenados do mais recente para o mais
     * antigo de maneira paginados.
     *
     * Retornará uma coleção vazia ou com a quantidade de itens menor que a
     * solicitada se o file system não possuir mais arquivos para leitura.
     *
     * @param int  $page
     * @param int  $per_page
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \RuntimeException
     */
    public function paginate(int $page, int $per_page): Collection
    {
        throw_if($page < 1 || $per_page < 1);

        return $this->get()
                    ->slice(
                        offset: ($page - 1) * $per_page,
                        length: $per_page
                    );
    }
}
