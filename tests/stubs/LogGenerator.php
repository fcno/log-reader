<?php

namespace Fcno\LogReader\Tests\Stubs;

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Gera arquivos de log fake.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogGenerator
{
    /**
     * @var string
     *
     * Data que será usada para a criação do arquivo de log.
     *
     * Ex.: yyyy-mm-dd
     */
    private $date = '';

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     *
     * File system onde os logs serão armazenados.
     */
    private $file_system;

    /**
     * @var array
     *
     * Definição do modelo de registro que será criado
     */
    private $definition = [
        'date' => null,
        'time' => null,
        'env' => null,
        'level' => null,
        'message' => null,
        'context' => null,
        'extra' => null,
    ];

    /**
     * Define o file system onde os logs serão armazenados.
     *
     * @param string  $disk nome do file system
     *
     * @return static
     */
    public static function on(string $disk): static
    {
        return new static($disk);
    }

    /**
     * Cria uma instância do objeto.
     *
     * @param string  $disk nome do file system
     */
    public function __construct(string $disk)
    {
        $this->file_system = Storage::disk($disk);
    }

    /**
     * Cria os registros de acordo com as definições informadas.
     *
     * @param array|null  $def
     *
     * @return static
     */
    public function create(?array $def): static
    {
        $this->setDefinition($def);

        return $this;
    }

    /**
     * Cria uma determinada quantidade de arquivos de logs
     *
     * @param int  $files quantidade de arquivos de logs que serão criada
     * @param int  $records quantidade de registros por arquivo de log
     *
     * @return static
     *
     * @throws RuntimeException  Se `$files < 1 || $records < 1`
     */
    public function count(int $files, int $records): static
    {
        throw_if($files < 1 || $records < 1);

        for ($i = 0; $i < $files; $i++) {
            $this->setDate($i);

            $this->file_system->put(
                $this->getFileName(),
                $this->getFileContents($records)
            );
        }

        return $this;
    }

    /**
     * Nova definição para a criação dos registros de log.
     *
     * Os valores informados por parâmetro irão sobrescrever os valores
     * presentes na definição padrão.
     * Notar que apenas serão sobrescritos os valores que possuírem as mesmas
     * chaves de definição.
     * Ou seja, se for informado um novo env, este será utilizado.
     * Por fim, não é possível sobrescrever a propriedade **date**.
     *
     * @param array|null  $def
     *
     * @return void
     */
    private function setDefinition(?array $def): void
    {
        if (! $def) {
            return;
        }

        $original = collect($this->definition);
        $new_def = collect($def)->forget('date');

        $original->transform(function ($item, $key) use ($new_def) {
            return $new_def->has($key)
                            ? $new_def->get($key)
                            : $item;
        });

        $this->definition = $original->toArray();
    }

    /**
     * Adiciona uma determinada quantidade de registros a um arquivo existente
     *
     * Todos os registros adicionados terão o mesmo level, porém os demais
     * dados do registro serão gerados de maneira randômica.
     *
     * @param string  $log_file arquivo de log que terá os registros inseridos
     * @param int  $records quantidade de registros a serem inseridos
     * @param string  $level level do registro
     *
     * @return static
     *
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     * @throws RuntimeException  Se `$records < 1`
     */
    public function appendLevel(string $log_file, int $records, string $level): static
    {
        throw_if($this->file_system->missing($log_file), FileNotFoundException::class);
        throw_if($records <= 0);

        $this->definition['level'] = $level;

        $this->file_system->append(
            $log_file,
            $this->getFileContents($records)
        );

        return $this;
    }

    /**
     * Define a data do log.
     *
     * A data será definida tomando-se como base a data atual e subtraindo-se
     * a quantidade de dias informados.
     * Esse data será usada para definir o nome do arquivo, bem como a data dos
     * registros em seu conteúdo.
     *
     * @return void
     */
    private function setDate(int $days_ago): void
    {
        $this->date = Carbon::now()
                            ->subDays($days_ago)
                            ->format('Y-m-d');

        $this->definition['date'] = $this->date;
    }

    /**
     * Nome do arquivo de log no padrão Laraval para log diário.
     *
     * Ex.: laravel-yyyy-mm-dd.log
     *
     * @return string
     */
    private function getFileName(): string
    {
        return Str::of('laravel-')
                    ->append(
                        Carbon::createFromFormat('Y-m-d', $this->date)->format('Y-m-d')
                    )
                    ->finish('.log');
    }

    /**
     * Define o conteúdo fake do arquivo de log.
     *
     * @param int  $records quantidade de registros por arquivo de log
     *
     * @return string
     */
    private function getFileContents(int $records): string
    {
        $amount = ($records <= 0)
                    ? rand(1, 20)
                    : $records;

        $records = collect();

        for ($i = 0; $i < $amount; $i++) {
            $records->push($this->getRecord());
        }

        return $records->join(PHP_EOL);
    }

    /**
     * Registro a ser inserido no arquivo de log
     *
     * O registro é uma linha no arquivo de log e representa um determinado log
     * ocorrido naquele dia.
     * Um arquivo de log pode ter inúmeros registros, um para cada evento de
     * interesse.
     *
     * @return string
     */
    private function getRecord(): string
    {
        return LogRecordTemplate::create()->getRecord($this->definition);
    }
}
