<?php

namespace Fcno\LogReader\Contracts;

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Regex;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
abstract class BaseContentReader implements IContentReader
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     *
     * File System onde estão armazenados os arquivos de log da aplicação.
     */
    protected $file_system;

    /**
     * @var string
     *
     * Nome do arquivo de log que está sendo trabalhado.
     *
     * Ex.: laravel-2020-12-30.log
     */
    protected $log_file;

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
     */
    public function infoAbout(string $log_file): static
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);

        $this->log_file = $log_file;

        return $this;
    }

    /**
     * Retorna um Generator ou LimitIterator de acordo com a necessidade ou não
     * paginação do resultado
     *
     * @return \LimitIterator|\Generator
     */
    abstract protected function getLineGenerator(): \LimitIterator|\Generator;

    /**
     * Filtra o array para conter apenas os índices de interesse.
     *
     * @param array  $data
     *
     * @return \Illuminate\Support\Collection
     */
    abstract protected function filteredData(array $data): Collection;

    /**
     * Lê o arquivo de log e o retorna como coleção.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function readLog(): Collection
    {
        $data = collect();
        $line_generator = $this->getLineGenerator();

        // Lê linha a linha o log. Boa prática não carregar tudo em memória.
        foreach ($line_generator as $record) {
            preg_match(
                Regex::PATTERN,
                (string) $record,
                $output_array
            );

            $data->push(
                $this->filteredData($output_array)
            );
        }

        return $data;
    }

    /**
     * Caminho completo do arquivo de log que está sendo trabalhado
     *
     * @return string  Full path
     */
    protected function getFullPath(): string
    {
        return $this->file_system->path($this->log_file);
    }
}
