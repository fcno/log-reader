<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
use Fcno\LogReader\Exceptions\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Manipular os arquivos de log do file system.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     *
     * File System onde estão armazenados os arquivos de log da aplicação.
     */
    private $file_system;

    /**
     * @var string
     *
     * Nome do arquivo de log que está sendo trabalhado.
     *
     * Ex.: laravel-2020-12-30.log
     */
    private $log_file;

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
     * Todos os arquivos de log do file system ordenados do mais recente para o
     * mais antigo.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return collect($this->file_system->files())
                ->sortDesc()
                ->values();
    }

    /**
     * Arquivos de log do file system ordenados do mais recente para o mais
     * antigo paginados.
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
