<?php

namespace Fcno\LogReader\Contracts;

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Fcno\LogReader\Regex;
use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
abstract class BaseContentReader extends BaseReader implements IContentReader
{
    /**
     * @var string
     *
     * Nome do arquivo de log diário que está sendo trabalhado.
     *
     * Ex.: laravel-2020-12-30.log
     */
    protected $log_file;

    /**
     * @inheritdoc
     */
    public function infoAbout(string $log_file): static
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);
        throw_if(! preg_match(Regex::LOG_FILE, $log_file), NotDailyLogException::class);

        $this->log_file = $log_file;

        return $this;
    }

    /**
     * Retorna um Generator ou LimitIterator de acordo com a necessidade ou não
     * paginação do resultado.
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
     * Lê o arquivo de log diário e o retorna como coleção.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function readLog(): Collection
    {
        $data = collect();
        $line_generator = $this->getLineGenerator();

        // Lê linha a linha do log. Boa prática não carregar tudo em memória.
        foreach ($line_generator as $record) {
            preg_match(
                Regex::RECORD,
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
     * Caminho completo do arquivo de log diário que está sendo trabalhado.
     *
     * @return string  Full path
     */
    protected function getFullPath(): string
    {
        return $this->file_system->path($this->log_file);
    }
}
