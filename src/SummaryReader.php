<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
use Fcno\LogReader\Exceptions\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class SummaryReader
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
     * Define o arquivo de log que será sumarizado.
     *
     * @param string  $log_file Ex.: laravel-2000-12-30.log
     *
     * @return static
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     */
    public function infoAbout(string $log_file): static
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);

        $this->log_file = $log_file;

        return $this;
    }

    /**
     * Sumário do arquivo de log.
     *
     * Sumário:
     * - date: string com a data do log Y-m-d
     * - debug: int quantidade de registros do tipo debug
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return $this->readyToGoSummary(
            $this->readLog()
        );
    }

    /**
     * Lê o arquivo de log e o retorna como coleção.
     *
     * @return \Illuminate\Support\Collection
     */
    private function readLog(): Collection
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
     * Retorna o Generator que percorrerá o arquivo linha a linha.
     *
     * @return \Generator
     */
    private function getLineGenerator(): \Generator
    {
        return LineReader::readLines($this->getFullPath());
    }

    /**
     * Filtra o array para conter apenas os índices de valores de interesse.
     *
     * @param array  $data
     *
     * @return \Illuminate\Support\Collection
     */
    private function filteredData(array $data): Collection
    {
        return collect($data)
                ->only(['date', 'level']);
    }

    /**
     * Prepara o sumário para ser retornado ao chamador.
     *
     * Sumariza:
     * - Data do log
     * - Quantidade de registros por level
     *
     * @param \Illuminate\Support\Collection  $summary_in_process
     *
     * @return \Illuminate\Support\Collection
     */
    private function readyToGoSummary(Collection $summary_in_process): Collection
    {
        $filtered = $summary_in_process->countBy('level');

        $filtered->put(
            'date',
            $summary_in_process->first()->get('date')
        );

        return $filtered;
    }

    /**
     * Caminho completo do arquivo de log que está sendo trabalhado
     *
     * @return string  Full path
     */
    private function getFullPath(): string
    {
        return $this->file_system->path($this->log_file);
    }
}
