<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
use Fcno\LogReader\Exceptions\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Manipular um arquivo de log para extrair seus registros completos.
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
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class RecordReader implements IReader
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
     * @var int
     *
     * Página da paginação que será exibida quando o resultado for paginado.
     */
    private $page;

    /**
     * @var int
     *
     * Quantidada de registros por página que serão exibidos quando da paginação.
     */
    private $per_page;

    /**
     * @inheritdoc
     */
    public function from(string $disk): static
    {
        $this->file_system = Storage::disk($disk);

        return $this;
    }

    /**
     * Define o arquivo de log que será trabalhado.
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
     * Registros do arquivo de log.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return $this->readLog();
    }

    /**
     * Registros do arquivo de log de maneira paginada.
     *
     * Retornará uma coleção vazia ou com a quantidade de itens menor que a
     * solicitada se o arquivo já tiver chegado ao final do arquivo.
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

        $this->page     = $page;
        $this->per_page = $per_page;

        return $this->readLog();
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
     * Retorna um line generator de acordo com a necessidade ou não de
     * paginação do resultado
     *
     * @return \LimitIterator|\Generator
     */
    private function getLineGenerator(): \LimitIterator|\Generator
    {
        $line_generator = LineReader::readLines($this->getFullPath());

        return ($this->page && $this->per_page)
        ? new \LimitIterator(
            iterator: $line_generator,
            offset: ($this->page - 1) * $this->per_page,
            limit: $this->per_page
        )
        : $line_generator;
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
