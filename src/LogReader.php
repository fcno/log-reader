<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
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
     * @var string  $log_file
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
     * Informações completas de todos os registros de um determinado log.
     *
     * Informações contidas em cada registro:
     * - date    - data do evento
     * - time    - hora do evento
     * - env     - ambiente em que o evento ocorreu
     * - level   - nível do evento nos termos da PSR-3
     * - message - mensagem
     * - context - mensagem de contexto
     * - extra   - dados extras sobre o evento
     *
     * @param string  $log_file Ex.: laravel-2000-12-30.log
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     */
    public function fullInfoAbout(string $log_file): Collection
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);

        $this->log_file = $log_file;

        return $this->readLog();
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
    public function getDailySummary(string $log_file): Collection
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);

        $this->log_file = $log_file;

        $summary = $this->readLog();

        return $this->readyToGoSummary($summary);
    }

    /**
     * Prepara o sumário para ser retornado ao chamador
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

        $filtered->put('date', $summary_in_process->first()->get('date'));

        return $filtered;
    }

    /**
     * Lê o arquivo de log informado e o retorna como coleção
     *
     * @return \Illuminate\Support\Collection
     */
    private function readLog(): Collection
    {
        $data = collect();

        // Lê linha a linha o log. Boa prática não carregar tudo em memória.
        foreach (LineReader::readLines($this->getFullPath()) as $record) {
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
     * Filtra o array informado permanecendo apenas os índices que integram o
     * registro.
     *
     * @param array  $data
     *
     * @return \Illuminate\Support\Collection
     */
    private function filteredData(array $data): Collection
    {
        return collect($data)
                ->only(['date', 'time', 'env', 'level', 'message', 'context', 'extra']);
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
